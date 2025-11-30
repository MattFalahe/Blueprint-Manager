<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintContainerConfig;
use BlueprintManager\Services\BlueprintService;

class BlueprintSettingsController extends Controller
{
    protected $blueprintService;

    public function __construct(BlueprintService $blueprintService)
    {
        $this->blueprintService = $blueprintService;
    }

    /**
     * Display settings page
     */
    public function index()
    {
        // Get all available corporations
        $corporations = $this->getAvailableCorporations();
        
        // Get all container configurations
        $configurations = BlueprintContainerConfig::with('corporation')
            ->orderBy('corporation_id')
            ->orderBy('priority', 'desc')
            ->orderBy('display_category')
            ->get();

        return view('blueprint-manager::settings', compact('corporations', 'configurations'));
    }

    /**
     * Get all available corporations with blueprints
     */
    private function getAvailableCorporations()
    {
        // Get corporations that have blueprints
        $corporationsWithBlueprints = DB::table('corporation_blueprints')
            ->select('corporation_id')
            ->distinct()
            ->pluck('corporation_id');

        // Get corporation info for these corps
        $corporations = DB::table('corporation_infos')
            ->whereIn('corporation_id', $corporationsWithBlueprints)
            ->select('corporation_id', 'name')
            ->orderBy('name')
            ->get();

        return $corporations;
    }

    /**
     * Store new container configuration
     */
    public function storeContainerConfig(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'corporation_id' => 'required|integer',
                'container_name' => 'required|string|max:255',
                'display_category' => 'required|string|max:100',
                'match_type' => 'required|in:exact,contains,starts_with',
                'priority' => 'nullable|integer|min:0|max:100',
                'enabled' => 'boolean',
            ]);

            // Set defaults
            $validated['priority'] = $validated['priority'] ?? 0;
            $validated['enabled'] = $request->has('enabled');

            // Check for duplicate
            $existing = BlueprintContainerConfig::where('corporation_id', $validated['corporation_id'])
                ->where('container_name', $validated['container_name'])
                ->where('match_type', $validated['match_type'])
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'A configuration with this container name and match type already exists for this corporation.'
                ], 422);
            }

            // Create configuration
            $config = BlueprintContainerConfig::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Container configuration added successfully.',
                'config' => $config->load('corporation')
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
                'message' => 'Failed to add configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing container configuration
     */
    public function updateContainerConfig(Request $request, BlueprintContainerConfig $config)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'container_name' => 'required|string|max:255',
                'display_category' => 'required|string|max:100',
                'match_type' => 'required|in:exact,contains,starts_with',
                'priority' => 'nullable|integer|min:0|max:100',
                'enabled' => 'boolean',
            ]);

            // Set defaults
            $validated['priority'] = $validated['priority'] ?? 0;
            $validated['enabled'] = $request->has('enabled');

            // Check for duplicate (excluding current config)
            $existing = BlueprintContainerConfig::where('corporation_id', $config->corporation_id)
                ->where('container_name', $validated['container_name'])
                ->where('match_type', $validated['match_type'])
                ->where('id', '!=', $config->id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'A configuration with this container name and match type already exists for this corporation.'
                ], 422);
            }

            // Update configuration
            $config->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Container configuration updated successfully.',
                'config' => $config->fresh()->load('corporation')
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
                'message' => 'Failed to update configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete container configuration
     */
    public function deleteContainerConfig(BlueprintContainerConfig $config)
    {
        try {
            $config->delete();

            return response()->json([
                'success' => true,
                'message' => 'Container configuration deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-detect blueprint containers for a corporation
     */
    public function detectContainers($corporationId)
    {
        try {
            $containers = $this->blueprintService->detectBlueprintContainers($corporationId);

            // Get existing configurations for this corp
            $existingConfigs = BlueprintContainerConfig::where('corporation_id', $corporationId)
                ->pluck('container_name')
                ->toArray();

            // Filter out already configured containers
            $newContainers = $containers->reject(function ($containerName) use ($existingConfigs) {
                return in_array($containerName, $existingConfigs);
            });

            return response()->json([
                'success' => true,
                'containers' => $newContainers->values(),
                'total_found' => $containers->count(),
                'new_containers' => $newContainers->count(),
                'already_configured' => count($existingConfigs)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to detect containers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get configurations for a specific corporation (AJAX)
     */
    public function getConfigsForCorporation($corporationId)
    {
        try {
            $configs = BlueprintContainerConfig::where('corporation_id', $corporationId)
                ->with('corporation')
                ->orderBy('priority', 'desc')
                ->orderBy('display_category')
                ->get();

            return response()->json([
                'success' => true,
                'configurations' => $configs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load configurations: ' . $e->getMessage()
            ], 500);
        }
    }
}
