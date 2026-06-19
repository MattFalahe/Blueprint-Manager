<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintRequest as BlueprintRequestModel;
use BlueprintManager\Services\BlueprintService;
use BlueprintManager\Services\DiscordNotificationService;
use BlueprintManager\Services\BlueprintAccessService;

class BlueprintRequestController extends Controller
{
    protected $blueprintService;
    protected $notificationService;
    protected $accessService;

    public function __construct(BlueprintService $blueprintService, DiscordNotificationService $notificationService, BlueprintAccessService $accessService)
    {
        $this->blueprintService = $blueprintService;
        $this->notificationService = $notificationService;
        $this->accessService = $accessService;
    }

    /**
     * Publish a request-lifecycle event to the Manager Core EventBus so
     * consumers (HR Manager) can build a per-member engagement signal.
     * No-op + swallowed when Manager Core isn't installed, so the request
     * workflow never depends on it. `character_id` is always the REQUESTER;
     * the manager who acted rides in `actor_character_id`.
     */
    private function publishLifecycleEvent(string $topic, BlueprintRequestModel $req, ?int $actorCharacterId = null): void
    {
        if (!class_exists(\ManagerCore\Topics::class)) {
            return;
        }
        try {
            \ManagerCore\Topics::publish($topic, [
                'request_id'         => (int) $req->id,
                'corporation_id'     => (int) $req->corporation_id,
                'character_id'       => (int) $req->character_id,
                'blueprint_type_id'  => (int) $req->blueprint_type_id,
                'quantity'           => (int) $req->quantity,
                'runs'               => $req->runs !== null ? (int) $req->runs : null,
                'status'             => (string) $req->status,
                'actor_character_id' => $actorCharacterId !== null ? (int) $actorCharacterId : null,
                'response_notes'     => $req->response_notes,
                'occurred_at'        => now()->toIso8601String(),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('[Blueprint Manager] EventBus publish failed: ' . $e->getMessage());
        }
    }

    /**
     * Display requests page
     */
    public function index()
    {
        $userCorpIds = $this->accessService->visibleCorporationIds();

        $corpQuery = DB::table('corporation_infos')
            ->join('corporation_blueprints', 'corporation_infos.corporation_id', '=', 'corporation_blueprints.corporation_id')
            ->select('corporation_infos.corporation_id', 'corporation_infos.name')
            ->distinct()
            ->orderBy('corporation_infos.name');

        if ($userCorpIds !== null) {
            $corpQuery->whereIn('corporation_infos.corporation_id', $userCorpIds);
        }

        $corporations = $corpQuery->get();

        // Resolve the user's SeAT main character. Requests are always
        // submitted from the main so the requester attribution matches
        // the user's recognised identity, not whichever alt was most
        // recently linked. Null when the user has not picked a main yet.
        $user = auth()->user();
        $mainCharacter = null;
        if ($user && !empty($user->main_character_id)) {
            $mainCharacter = DB::table('character_infos')
                ->where('character_id', $user->main_character_id)
                ->select('character_id', 'name')
                ->first();
        }

        // Check if user can manage requests
        $canManageRequests = $user ? $user->can('blueprint-manager.manage_requests') : false;

        return view('blueprint-manager::requests', compact('corporations', 'mainCharacter', 'canManageRequests'));
    }

    /**
     * Get the acting character ID for management actions (approve / reject /
     * fulfill).
     *
     * Resolves to the SeAT main character (users.main_character_id) so the
     * "Approved by / Rejected by / Fulfilled by" attribution in Discord
     * notifications and request history always shows the user's recognised
     * identity. Falls back to the most recently linked character only when
     * no main is set, to keep the action functional during edge cases.
     */
    private function getUserCharacterId()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (!empty($user->main_character_id)) {
            return $user->main_character_id;
        }

        $character = DB::table('refresh_tokens')
            ->where('user_id', $user->id)
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
            // Validate input. character_id is NOT accepted from the form —
            // the requester is always resolved server-side to the user's
            // SeAT main character.
            $validated = $request->validate([
                'corporation_id' => 'required|integer',
                'blueprint_type_id' => 'required|integer',
                'quantity' => 'required|integer|min:1|max:1000',
                'runs' => 'nullable|integer|min:1',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Resolve the requesting character to the user's SeAT main so
            // notifications and history consistently show the user's
            // recognised identity, not a recently-linked alt.
            $user = auth()->user();
            $characterId = $user ? $user->main_character_id : null;

            if (empty($characterId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No main character is set on your SeAT account. Please set one in your SeAT profile before submitting blueprint requests.'
                ], 400);
            }

            // Verify user has access to this corporation (own corp or a corp
            // whose library has been shared with them).
            $userCorpIds = $this->accessService->visibleCorporationIds();
            if ($userCorpIds !== null && !in_array($validated['corporation_id'], $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this corporation'
                ], 403);
            }

            // Create request — character_id is the server-resolved main,
            // not anything the form sent.
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

            // Publish to Manager Core EventBus (consumers like HR Manager).
            $this->publishLifecycleEvent('blueprint.request.created', $blueprintRequest);

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
            
            // Get all user's character IDs for filtering "my requests"
            $userCharacterIds = DB::table('refresh_tokens')
                ->where('user_id', auth()->id())
                ->whereNull('deleted_at')
                ->pluck('character_id')
                ->toArray();
            
            // Build query
            $query = BlueprintRequestModel::with(['blueprintType', 'character', 'corporation', 'approver', 'fulfiller'])
                ->orderBy('created_at', 'desc');

            if ($viewType === 'my') {
                // Show only user's requests (from ANY of their characters)
                $query->whereIn('character_id', $userCharacterIds);
            } else {
                // Show all requests user has access to (managers)
                if (!auth()->user()->can('blueprint-manager.manage_requests')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
                
                // Managers only ever see requests for corps they belong to.
                // Library sharing grants view/request access, never request
                // management, so this uses the manageable (own-corp) set.
                $userCorpIds = $this->accessService->manageableCorporationIds();
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
            $data = $requests->map(function ($req) use ($userCharacterIds) {
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
                    'is_own' => in_array($req->character_id, $userCharacterIds),
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

            $this->publishLifecycleEvent('blueprint.request.approved', $blueprintRequest, $characterId);

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

            $this->publishLifecycleEvent('blueprint.request.rejected', $blueprintRequest, $characterId);

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

            $this->publishLifecycleEvent('blueprint.request.fulfilled', $blueprintRequest, $characterId);

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
            // Get all user's character IDs
            $userCharacterIds = DB::table('refresh_tokens')
                ->where('user_id', auth()->id())
                ->whereNull('deleted_at')
                ->pluck('character_id')
                ->toArray();

            // Check if user owns this request or has manage permission
            $canManage = auth()->user()->can('blueprint-manager.manage_requests');
            $isOwner = in_array($blueprintRequest->character_id, $userCharacterIds);

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
            // Verify user has access (own corp or a shared library)
            $userCorpIds = $this->accessService->visibleCorporationIds();
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
