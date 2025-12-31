<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintRequest;
use Carbon\Carbon;

class BlueprintStatisticsController extends Controller
{
    /**
     * Display statistics page
     */
    public function index()
    {
        try {
            // Get corporations the user has access to
            $userCorpIds = $this->getUserCorporations();
            
            if ($userCorpIds === null) {
                // Superadmin - get all corporations
                $corporations = DB::table('corporation_infos')
                    ->select('corporation_id', 'name')
                    ->orderBy('name')
                    ->get();
            } else {
                // Get only user's corporations
                $corporations = DB::table('corporation_infos')
                    ->whereIn('corporation_id', $userCorpIds)
                    ->select('corporation_id', 'name')
                    ->orderBy('name')
                    ->get();
            }

            // If no corporations found, return empty collection
            if (!$corporations) {
                $corporations = collect([]);
            }

            return view('blueprint-manager::statistics', compact('corporations'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Blueprint Manager Statistics Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return view with empty corporations
            $corporations = collect([]);
            return view('blueprint-manager::statistics', compact('corporations'))
                ->with('error', 'Unable to load statistics. Please check logs for details.');
        }
    }

    /**
     * Get overall statistics
     */
    public function getOverallStats()
    {
        try {
            $userCorpIds = $this->getUserCorporations();
            
            $query = BlueprintRequest::query();
            
            if ($userCorpIds !== null && !empty($userCorpIds)) {
                $query->whereIn('corporation_id', $userCorpIds);
            }

            // Clone query for each count to avoid conflicts
            $totalRequests = (clone $query)->count();
            $pendingRequests = (clone $query)->where('status', 'pending')->count();
            $approvedRequests = (clone $query)->where('status', 'approved')->count();
            $fulfilledRequests = (clone $query)->where('status', 'fulfilled')->count();
            $rejectedRequests = (clone $query)->where('status', 'rejected')->count();
            
            // Get unique requesters
            $uniqueRequesters = (clone $query)->distinct('character_id')->count('character_id');
            
            // Get time-based stats
            $requestsLast7Days = (clone $query)->where('created_at', '>=', Carbon::now()->subDays(7))->count();
            $requestsLast30Days = (clone $query)->where('created_at', '>=', Carbon::now()->subDays(30))->count();

            return response()->json([
                'total_requests' => $totalRequests,
                'pending' => $pendingRequests,
                'approved' => $approvedRequests,
                'fulfilled' => $fulfilledRequests,
                'rejected' => $rejectedRequests,
                'unique_requesters' => $uniqueRequesters,
                'last_7_days' => $requestsLast7Days,
                'last_30_days' => $requestsLast30Days,
            ]);
        } catch (\Exception $e) {
            \Log::error('Blueprint Manager - Error getting overall stats: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to load statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get character statistics
     */
    public function getCharacterStats()
    {
        try {
            \Log::info('Blueprint Manager: Starting getCharacterStats()');
            
            $userCorpIds = $this->getUserCorporations();
            \Log::info('Blueprint Manager: User corporations', ['corp_ids' => $userCorpIds]);
            
            $query = DB::table('blueprint_requests')
                ->join('character_infos', 'blueprint_requests.character_id', '=', 'character_infos.character_id')
                ->select(
                    'blueprint_requests.character_id',
                    'character_infos.name as character_name',
                    DB::raw('COUNT(*) as total_requests'),
                    DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled_count'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('MAX(blueprint_requests.created_at) as last_request'),
                    DB::raw('MIN(blueprint_requests.created_at) as first_request')
                );

            if ($userCorpIds !== null && !empty($userCorpIds)) {
                $query->whereIn('blueprint_requests.corporation_id', $userCorpIds);
                \Log::info('Blueprint Manager: Filtering by corporation IDs');
            }

            \Log::info('Blueprint Manager: Executing character stats query');
            $stats = $query->groupBy('blueprint_requests.character_id', 'character_infos.name')
                ->orderByDesc('total_requests')
                ->get();
            
            \Log::info('Blueprint Manager: Got character stats', ['count' => $stats->count()]);

            // Calculate rejection rate and add abuse indicators
            $stats = $stats->map(function ($stat) {
                $stat->rejection_rate = $stat->total_requests > 0 
                    ? round(($stat->rejected_count / $stat->total_requests) * 100, 1) 
                    : 0;
                
                // Calculate requests per day (for recent activity)
                $daysSinceFirst = Carbon::parse($stat->first_request)->diffInDays(Carbon::now());
                $stat->requests_per_day = $daysSinceFirst > 0 
                    ? round($stat->total_requests / $daysSinceFirst, 2) 
                    : 0;
                
                // Flag potential abuse
                $stat->abuse_indicators = [];
                
                if ($stat->rejection_rate > 50 && $stat->total_requests > 5) {
                    $stat->abuse_indicators[] = 'High rejection rate';
                }
                
                if ($stat->requests_per_day > 5) {
                    $stat->abuse_indicators[] = 'High request frequency';
                }
                
                if ($stat->total_quantity > 1000) {
                    $stat->abuse_indicators[] = 'Very high total quantity';
                }

                return $stat;
            });

            \Log::info('Blueprint Manager: Character stats processed successfully');
            return response()->json($stats);
            
        } catch (\Exception $e) {
            \Log::error('Blueprint Manager - Error getting character stats: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to load character statistics',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get blueprint popularity statistics
     */
    public function getBlueprintStats()
    {
        try {
            \Log::info('Blueprint Manager: Starting getBlueprintStats()');
            
            $userCorpIds = $this->getUserCorporations();
            
            $query = DB::table('blueprint_requests')
                ->join('invTypes', 'blueprint_requests.blueprint_type_id', '=', 'invTypes.typeID')
                ->select(
                    'blueprint_requests.blueprint_type_id',
                    'invTypes.typeName as blueprint_name',
                    DB::raw('COUNT(*) as request_count'),
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('COUNT(DISTINCT character_id) as unique_requesters'),
                    DB::raw('SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled_count'),
                    DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('MAX(blueprint_requests.created_at) as last_requested')
                );

            if ($userCorpIds !== null && !empty($userCorpIds)) {
                $query->whereIn('blueprint_requests.corporation_id', $userCorpIds);
            }

            $stats = $query->groupBy('blueprint_requests.blueprint_type_id', 'invTypes.typeName')
                ->orderByDesc('request_count')
                ->limit(50)
                ->get();

            \Log::info('Blueprint Manager: Blueprint stats retrieved', ['count' => $stats->count()]);
            return response()->json($stats);
            
        } catch (\Exception $e) {
            \Log::error('Blueprint Manager - Error getting blueprint stats: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Failed to load blueprint statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed character request history
     */
    public function getCharacterDetails($characterId)
    {
        $userCorpIds = $this->getUserCorporations();
        
        $query = BlueprintRequest::with(['blueprintType', 'corporation'])
            ->where('character_id', $characterId);

        if ($userCorpIds !== null) {
            $query->whereIn('corporation_id', $userCorpIds);
        }

        $requests = $query->orderByDesc('created_at')->get();

        return response()->json($requests);
    }

    /**
     * Get time-based statistics for charts
     */
    public function getTimeSeriesStats($days = 30)
    {
        $userCorpIds = $this->getUserCorporations();
        
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        
        $query = DB::table('blueprint_requests')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "fulfilled" THEN 1 ELSE 0 END) as fulfilled'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected')
            )
            ->where('created_at', '>=', $startDate);

        if ($userCorpIds !== null) {
            $query->whereIn('corporation_id', $userCorpIds);
        }

        $stats = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get corporation-specific statistics
     */
    public function getCorporationStats($corporationId)
    {
        $userCorpIds = $this->getUserCorporations();
        
        // Check access
        if ($userCorpIds !== null && !in_array($corporationId, $userCorpIds)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $stats = [
            'total_requests' => BlueprintRequest::where('corporation_id', $corporationId)->count(),
            'pending' => BlueprintRequest::where('corporation_id', $corporationId)->where('status', 'pending')->count(),
            'approved' => BlueprintRequest::where('corporation_id', $corporationId)->where('status', 'approved')->count(),
            'fulfilled' => BlueprintRequest::where('corporation_id', $corporationId)->where('status', 'fulfilled')->count(),
            'rejected' => BlueprintRequest::where('corporation_id', $corporationId)->where('status', 'rejected')->count(),
            'unique_requesters' => BlueprintRequest::where('corporation_id', $corporationId)
                ->distinct('character_id')
                ->count('character_id'),
        ];

        return response()->json($stats);
    }

    /**
     * Get user's accessible corporation IDs
     */
    private function getUserCorporations()
    {
        try {
            $user = auth()->user();
            
            // If no authenticated user, return empty array
            if (!$user) {
                \Log::warning('Blueprint Manager: No authenticated user found');
                return [];
            }
            
            // Check if user is superuser/admin
            // Handle both old and new role checking methods
            $isSuperuser = false;
            if (method_exists($user, 'hasRole')) {
                $isSuperuser = $user->hasRole('Superuser') || $user->hasRole('Administrator');
            } elseif (method_exists($user, 'isAdmin')) {
                $isSuperuser = $user->isAdmin();
            } elseif (property_exists($user, 'admin') && $user->admin) {
                $isSuperuser = true;
            }
            
            if ($isSuperuser) {
                return null; // null means all corporations
            }

            $corporationIds = DB::table('refresh_tokens')
                ->join('character_affiliations', 'refresh_tokens.character_id', '=', 'character_affiliations.character_id')
                ->where('refresh_tokens.user_id', $user->id)
                ->whereNull('refresh_tokens.deleted_at')
                ->pluck('character_affiliations.corporation_id')
                ->unique()
                ->filter()
                ->toArray();
            
            return !empty($corporationIds) ? $corporationIds : [];
        } catch (\Exception $e) {
            \Log::error('Blueprint Manager - Error getting user corporations: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
