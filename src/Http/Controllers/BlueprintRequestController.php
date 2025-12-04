<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintRequest as BlueprintRequestModel;
use BlueprintManager\Services\BlueprintService;
use BlueprintManager\Services\DiscordNotificationService;

class BlueprintRequestController extends Controller
{
    protected $blueprintService;
    protected $notificationService;

    public function __construct(BlueprintService $blueprintService, DiscordNotificationService $notificationService)
    {
        $this->blueprintService = $blueprintService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display requests page
     */
    public function index()
    {
        // Get corporations the user has access to
        $userCorpIds = $this->getUserCorporations();
        
        if ($userCorpIds === null) {
            // Superadmin - get all corporations with blueprints
            $corporations = DB::table('corporation_infos')
                ->join('corporation_blueprints', 'corporation_infos.corporation_id', '=', 'corporation_blueprints.corporation_id')
                ->select('corporation_infos.corporation_id', 'corporation_infos.name')
                ->distinct()
                ->orderBy('corporation_infos.name')
                ->get();
        } else {
            // Get only user's corporations that have blueprints
            $corporations = DB::table('corporation_infos')
                ->join('corporation_blueprints', 'corporation_infos.corporation_id', '=', 'corporation_blueprints.corporation_id')
                ->whereIn('corporation_infos.corporation_id', $userCorpIds)
                ->select('corporation_infos.corporation_id', 'corporation_infos.name')
                ->distinct()
                ->orderBy('corporation_infos.name')
                ->get();
        }

        // Check if user can manage requests
        $canManageRequests = auth()->user()->can('blueprint-manager.manage_requests');

        return view('blueprint-manager::requests', compact('corporations', 'canManageRequests'));
    }

    /**
     * Get user's accessible corporation IDs
     */
    private function getUserCorporations()
    {
        $corporationIds = DB::table('refresh_tokens')
            ->join('character_affiliations', 'refresh_tokens.character_id', '=', 'character_affiliations.character_id')
            ->where('refresh_tokens.user_id', auth()->id())
            ->whereNull('refresh_tokens.deleted_at')
            ->pluck('character_affiliations.corporation_id')
            ->unique()
            ->filter()
            ->toArray();
        
        return !empty($corporationIds) ? $corporationIds : null;
    }

    /**
     * Get user's character ID (primary character)
     */
    private function getUserCharacterId()
    {
        $character = DB::table('refresh_tokens')
            ->where('user_id', auth()->id())
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $character ? $character->character_id : null;
    }

    /**
     * Store new blueprint request
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'corporation_id' => 'required|integer',
                'blueprint_type_id' => 'required|integer',
                'quantity' => 'required|integer|min:1|max:1000',
                'runs' => 'nullable|integer|min:1',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Verify user has access to this corporation
            $userCorpIds = $this->getUserCorporations();
            if ($userCorpIds !== null && !in_array($validated['corporation_id'], $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this corporation'
                ], 403);
            }

            // Get user's character ID
            $characterId = $this->getUserCharacterId();
            if (!$characterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No character found for this user'
                ], 400);
            }

            // Create request
            $blueprintRequest = BlueprintRequestModel::create([
                'corporation_id' => $validated['corporation_id'],
                'character_id' => $characterId,
                'blueprint_type_id' => $validated['blueprint_type_id'],
                'quantity' => $validated['quantity'],
                'runs' => $validated['runs'],
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            // Send Discord notification
            $this->notificationService->notifyRequestCreated($blueprintRequest);

            return response()->json([
                'success' => true,
                'message' => 'Blueprint request submitted successfully.',
                'request' => $blueprintRequest->load(['blueprintType', 'character', 'corporation'])
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get requests data for DataTables (AJAX)
     */
    public function getRequestsData(Request $request)
    {
        try {
            $viewType = $request->get('view_type', 'my'); // 'my' or 'manage'
            $status = $request->get('status', ''); // Filter by status
            
            // Get user's character ID
            $characterId = $this->getUserCharacterId();
            
            // Build query
            $query = BlueprintRequestModel::with(['blueprintType', 'character', 'corporation', 'approver', 'fulfiller'])
                ->orderBy('created_at', 'desc');

            if ($viewType === 'my') {
                // Show only user's requests
                $query->where('character_id', $characterId);
            } else {
                // Show all requests user has access to (managers)
                if (!auth()->user()->can('blueprint-manager.manage_requests')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
                
                // Filter by user's corporations if not superadmin
                $userCorpIds = $this->getUserCorporations();
                if ($userCorpIds !== null) {
                    $query->whereIn('corporation_id', $userCorpIds);
                }
            }

            // Filter by status if provided
            if ($status) {
                $query->where('status', $status);
            }

            $requests = $query->get();

            // Format data for DataTables
            $data = $requests->map(function ($req) use ($characterId) {
                return [
                    'id' => $req->id,
                    'corporation_name' => $req->corporation->name ?? 'Unknown',
                    'blueprint_name' => $req->blueprintType->typeName ?? 'Unknown',
                    'blueprint_type_id' => $req->blueprint_type_id,
                    'character_name' => $req->character->name ?? 'Unknown',
                    'character_id' => $req->character_id,
                    'quantity' => $req->quantity,
                    'runs' => $req->runs,
                    'status' => $req->status,
                    'notes' => $req->notes,
                    'response_notes' => $req->response_notes,
                    'created_at' => $req->created_at->format('Y-m-d H:i'),
                    'approved_by' => $req->approver->name ?? null,
                    'approved_at' => $req->approved_at ? $req->approved_at->format('Y-m-d H:i') : null,
                    'fulfilled_by' => $req->fulfiller->name ?? null,
                    'fulfilled_at' => $req->fulfilled_at ? $req->fulfilled_at->format('Y-m-d H:i') : null,
                    'is_own' => $req->character_id == $characterId,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a request
     */
    public function approve(Request $request, BlueprintRequestModel $blueprintRequest)
    {
        try {
            // Verify permission
            if (!auth()->user()->can('blueprint-manager.manage_requests')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Verify request is pending
            if ($blueprintRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not pending'
                ], 400);
            }

            // Get character ID
            $characterId = $this->getUserCharacterId();
            if (!$characterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No character found'
                ], 400);
            }

            // Approve request
            $blueprintRequest->approve($characterId, $request->input('notes'));

            // Get approver name for notification
            $approverName = DB::table('character_infos')
                ->where('character_id', $characterId)
                ->value('name');

            // Send Discord notification
            $this->notificationService->notifyRequestApproved(
                $blueprintRequest,
                $approverName ?? 'Unknown',
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Request approved successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a request
     */
    public function reject(Request $request, BlueprintRequestModel $blueprintRequest)
    {
        try {
            // Verify permission
            if (!auth()->user()->can('blueprint-manager.manage_requests')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Verify request is pending
            if ($blueprintRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not pending'
                ], 400);
            }

            // Validate notes required for rejection
            $request->validate([
                'notes' => 'required|string|max:1000',
            ]);

            // Get character ID
            $characterId = $this->getUserCharacterId();
            if (!$characterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No character found'
                ], 400);
            }

            // Reject request
            $blueprintRequest->reject($characterId, $request->input('notes'));

            // Get rejector name for notification
            $rejectorName = DB::table('character_infos')
                ->where('character_id', $characterId)
                ->value('name');

            // Send Discord notification
            $this->notificationService->notifyRequestRejected(
                $blueprintRequest,
                $rejectorName ?? 'Unknown',
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Request rejected.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notes are required for rejection.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fulfill a request
     */
    public function fulfill(Request $request, BlueprintRequestModel $blueprintRequest)
    {
        try {
            // Verify permission
            if (!auth()->user()->can('blueprint-manager.manage_requests')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Verify request is approved
            if ($blueprintRequest->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Request is not approved'
                ], 400);
            }

            // Get character ID
            $characterId = $this->getUserCharacterId();
            if (!$characterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No character found'
                ], 400);
            }

            // Fulfill request
            $blueprintRequest->fulfill($characterId, $request->input('notes'));

            // Get fulfiller name for notification
            $fulfillerName = DB::table('character_infos')
                ->where('character_id', $characterId)
                ->value('name');

            // Send Discord notification
            $this->notificationService->notifyRequestFulfilled(
                $blueprintRequest,
                $fulfillerName ?? 'Unknown',
                $request->input('notes')
            );

            return response()->json([
                'success' => true,
                'message' => 'Request marked as fulfilled.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fulfill request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a request (user can delete their own pending requests)
     */
    public function destroy(BlueprintRequestModel $blueprintRequest)
    {
        try {
            // Get user's character ID
            $characterId = $this->getUserCharacterId();
            if (!$characterId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No character found'
                ], 400);
            }

            // Check if user owns this request or has manage permission
            $canManage = auth()->user()->can('blueprint-manager.manage_requests');
            $isOwner = $blueprintRequest->character_id == $characterId;

            if (!$canManage && !$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied - you can only delete your own requests'
                ], 403);
            }

            // Only allow deleting pending or rejected requests
            if (!in_array($blueprintRequest->status, ['pending', 'rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending or rejected requests can be deleted'
                ], 400);
            }

            $blueprintRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available blueprints for a corporation (AJAX)
     */
    public function getAvailableBlueprints($corporationId)
    {
        try {
            // Verify user has access
            $userCorpIds = $this->getUserCorporations();
            if ($userCorpIds !== null && !in_array($corporationId, $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Get all blueprints for this corporation
            $blueprints = $this->blueprintService->getBlueprintsByCategory($corporationId);

            // Format for Select2
            $options = $blueprints->map(function ($bp) {
                return [
                    'id' => $bp->type_id,
                    'text' => $bp->type_name . ' (' . ($bp->is_bpo ? 'BPO' : 'BPC') . ' - ' . $bp->category . ')',
                    'category' => $bp->category,
                    'is_bpo' => $bp->is_bpo,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'blueprints' => $options
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load blueprints: ' . $e->getMessage()
            ], 500);
        }
    }

}
