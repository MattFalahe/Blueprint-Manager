<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Services\BlueprintService;
use BlueprintManager\Services\ResearchProgressService;

class BlueprintLibraryController extends Controller
{
    protected $blueprintService;
    protected $researchService;

    public function __construct(
        BlueprintService $blueprintService,
        ResearchProgressService $researchService
    ) {
        $this->blueprintService = $blueprintService;
        $this->researchService = $researchService;
    }

    /**
     * Display blueprint library page
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

        return view('blueprint-manager::library', compact('corporations'));
    }

    /**
     * Get user's accessible corporation IDs
     */
    private function getUserCorporations()
    {
        // Get corporation IDs from user's characters via refresh_tokens and character_affiliations
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
     * Get blueprints data for DataTables (AJAX)
     */
    public function getBlueprintsData($corporationId)
    {
        try {
            // Verify user has access to this corporation
            $userCorpIds = $this->getUserCorporations();
            if ($userCorpIds !== null && !in_array($corporationId, $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this corporation'
                ], 403);
            }

            // Get category filter if provided
            $category = request()->get('category');

            // Get blueprints
            $blueprints = $this->blueprintService->getBlueprintsByCategory($corporationId, $category);

            // Get research jobs for this corporation
            $researchJobs = $this->researchService->getActiveResearchJobs($corporationId);
            
            // Create lookup for research status
            $researchByType = $researchJobs->groupBy('blueprint_type_id');

            // Format data for DataTables
            $data = $blueprints->map(function ($blueprint) use ($researchByType) {
                $typeId = $blueprint->type_id;
                $hasResearch = $researchByType->has($typeId);
                
                $researchInfo = [];
                if ($hasResearch) {
                    $jobs = $researchByType->get($typeId);
                    $researchInfo = $jobs->map(function ($job) {
                        return [
                            'activity' => $job->activity_name,
                            'description' => $job->job_description,
                            'progress' => $job->progress_percentage,
                            'time_remaining' => $job->time_remaining,
                        ];
                    })->toArray();
                }

                return [
                    'type_id' => $typeId,
                    'type_name' => $blueprint->type_name,
                    'category' => $blueprint->category,
                    'is_bpo' => $blueprint->is_bpo,
                    'quantity' => $blueprint->quantity,
                    'me_min' => $blueprint->min_me,
                    'me_max' => $blueprint->max_me,
                    'me_avg' => $blueprint->avg_me,
                    'te_min' => $blueprint->min_te,
                    'te_max' => $blueprint->max_te,
                    'te_avg' => $blueprint->avg_te,
                    'runs' => $blueprint->runs,
                    'locations' => $blueprint->locations,
                    'has_research' => $hasResearch,
                    'research_jobs' => $researchInfo,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->values()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load blueprints: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories for a corporation (AJAX)
     */
    public function getCategories($corporationId)
    {
        try {
            // Verify user has access to this corporation
            $userCorpIds = $this->getUserCorporations();
            if ($userCorpIds !== null && !in_array($corporationId, $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this corporation'
                ], 403);
            }

            $categories = $this->blueprintService->getCategoriesForCorporation($corporationId);

            // Get counts per category
            $categoriesWithCounts = $categories->mapWithKeys(function ($category) use ($corporationId) {
                $blueprints = $this->blueprintService->getBlueprintsByCategory($corporationId, $category);
                return [$category => $blueprints->count()];
            });

            return response()->json([
                'success' => true,
                'categories' => $categoriesWithCounts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blueprint details for modal (AJAX)
     */
    public function getBlueprintDetails($corporationId, $typeId)
    {
        try {
            // Verify user has access to this corporation
            $userCorpIds = $this->getUserCorporations();
            if ($userCorpIds !== null && !in_array($corporationId, $userCorpIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this corporation'
                ], 403);
            }

            $details = $this->blueprintService->getBlueprintDetails($corporationId, $typeId);
            
            // Get research jobs for this specific blueprint type
            $researchJobs = $this->researchService->getJobsForBlueprint($corporationId, $typeId);

            // Format locations
            $locations = $details->groupBy('location_id')->map(function ($group) {
                $first = $group->first();
                return [
                    'location_id' => $first->location_id,
                    'container_name' => $first->container_name,
                    'quantity' => $group->count(),
                    'blueprints' => $group->map(function ($bp) {
                        return [
                            'item_id' => $bp->item_id,
                            'material_efficiency' => $bp->material_efficiency,
                            'time_efficiency' => $bp->time_efficiency,
                            'runs' => $bp->runs,
                            'quantity' => $bp->quantity,
                        ];
                    })->toArray()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'type_name' => $details->first()->type->typeName ?? 'Unknown',
                'total_quantity' => $details->count(),
                'locations' => $locations,
                'research_jobs' => $researchJobs->map(function ($job) {
                    return [
                        'activity' => $job->activity_name,
                        'description' => $job->job_description,
                        'progress' => $job->progress_percentage,
                        'time_remaining' => $job->time_remaining,
                        'current_me' => $job->current_me,
                        'current_te' => $job->current_te,
                    ];
                })->toArray()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load blueprint details: ' . $e->getMessage()
            ], 500);
        }
    }
}
