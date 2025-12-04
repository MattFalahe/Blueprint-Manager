<?php

namespace BlueprintManager\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintContainerConfig;
use BlueprintManager\Models\BlueprintDetectionSettings;
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
            ->orderBy('name')
            ->get();

        return $corporations;
    }

    /**
     * Store a new container configuration
     */
    public function storeContainerConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'corporation_id' => 'required|integer',
                'container_name' => 'required|string|max:255',
                'match_type' => 'required|in:exact,contains,starts_with,ends_with,regex',
                'display_category' => 'required|string|max:100',
                'enabled' => 'boolean',
                'priority' => 'integer|min:0|max:100',
            ]);

            // Check for duplicates
            $exists = BlueprintContainerConfig::where('corporation_id', $validated['corporation_id'])
                ->where('container_name', $validated['container_name'])
                ->where('match_type', $validated['match_type'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This configuration already exists'
                ], 422);
            }

            $config = BlueprintContainerConfig::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Configuration created successfully',
                'config' => $config->load('corporation')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing configuration
     */
    public function updateContainerConfig(Request $request, $id)
    {
        try {
            $config = BlueprintContainerConfig::findOrFail($id);

            $validated = $request->validate([
                'container_name' => 'sometimes|string|max:255',
                'match_type' => 'sometimes|in:exact,contains,starts_with,ends_with,regex',
                'display_category' => 'sometimes|string|max:100',
                'enabled' => 'boolean',
                'priority' => 'integer|min:0|max:100',
            ]);

            $config->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Configuration updated successfully',
                'config' => $config->load('corporation')
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a configuration
     */
    public function deleteContainerConfig($id)
    {
        try {
            $config = BlueprintContainerConfig::findOrFail($id);
            $config->delete();

            return response()->json([
                'success' => true,
                'message' => 'Configuration deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Configuration not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle configuration enabled status
     */
    public function toggleEnabled($id)
    {
        try {
            $config = BlueprintContainerConfig::findOrFail($id);
            $config->enabled = !$config->enabled;
            $config->save();

            return response()->json([
                'success' => true,
                'enabled' => $config->enabled,
                'message' => $config->enabled ? 'Configuration enabled' : 'Configuration disabled'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto-detect blueprint containers for a corporation
     */
    public function detectContainers($corporationId)
    {
        try {
            // Get detection settings for hangar filter
            $settings = BlueprintDetectionSettings::find($corporationId);
            $hangarFilter = $settings ? $settings->getEnabledDivisions() : null;

            // Detect containers with hangar filter
            $containers = $this->blueprintService->detectBlueprintContainers($corporationId, $hangarFilter);

            // Get existing configurations for this corp
            $existingConfigs = BlueprintContainerConfig::where('corporation_id', $corporationId)
                ->pluck('container_name')
                ->toArray();

            // Filter out already configured containers
            $newContainers = $containers->reject(function ($container) use ($existingConfigs) {
                return in_array($container->container_name, $existingConfigs);
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
                'configs' => $configs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load configurations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detection settings for a corporation (AJAX)
     */
    public function getDetectionSettings($corporationId)
    {
        try {
            $settings = BlueprintDetectionSettings::getOrCreateDefault($corporationId);
            $divisionNames = BlueprintDetectionSettings::getAvailableDivisionsWithNames($corporationId);

            return response()->json([
                'success' => true,
                'hangar_divisions' => $settings->getEnabledDivisions(),
                'division_names' => $divisionNames
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load detection settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save detection settings for a corporation (AJAX)
     */
    public function saveDetectionSettings(Request $request, $corporationId)
    {
        try {
            $validated = $request->validate([
                'hangar_divisions' => 'required|array',
                'hangar_divisions.*' => 'string|in:CorpSAG1,CorpSAG2,CorpSAG3,CorpSAG4,CorpSAG5,CorpSAG6,CorpSAG7,AssetSafety'
            ]);

            $settings = BlueprintDetectionSettings::updateOrCreate(
                ['corporation_id' => $corporationId],
                ['hangar_divisions' => $validated['hangar_divisions']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Detection settings saved successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save detection settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get webhook configurations
     */
    public function getWebhookConfigs()
    {
        $configs = \BlueprintManager\Models\WebhookConfig::with('corporation')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'configs' => $configs
        ]);
    }

    /**
     * Store new webhook configuration
     */
    public function storeWebhookConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'webhook_url' => 'required|url',
                'corporation_id' => 'nullable|exists:corporation_infos,corporation_id',
                'notify_created' => 'required|boolean',
                'notify_approved' => 'required|boolean',
                'notify_rejected' => 'required|boolean',
                'notify_fulfilled' => 'required|boolean',
                'ping_role_created' => 'nullable|string|max:50',
                'ping_role_approved' => 'nullable|string|max:50',
                'ping_role_rejected' => 'nullable|string|max:50',
                'ping_role_fulfilled' => 'nullable|string|max:50',
                'enabled' => 'required|boolean',
            ]);
    
            // Remove empty role IDs
            foreach (['ping_role_created', 'ping_role_approved', 'ping_role_rejected', 'ping_role_fulfilled'] as $field) {
                if (isset($validated[$field]) && empty(trim($validated[$field]))) {
                    $validated[$field] = null;
                }
            }
    
            $config = \BlueprintManager\Models\WebhookConfig::create($validated);
    
            return response()->json([
                'success' => true,
                'message' => 'Webhook configuration created successfully',
                'config' => $config->load('corporation')
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create webhook configuration: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update webhook configuration
     */
    public function updateWebhookConfig(Request $request, $id)
    {
        try {
            $config = \BlueprintManager\Models\WebhookConfig::findOrFail($id);
    
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'webhook_url' => 'required|url',
                'corporation_id' => 'nullable|exists:corporation_infos,corporation_id',
                'notify_created' => 'required|boolean',
                'notify_approved' => 'required|boolean',
                'notify_rejected' => 'required|boolean',
                'notify_fulfilled' => 'required|boolean',
                'ping_role_created' => 'nullable|string|max:50',
                'ping_role_approved' => 'nullable|string|max:50',
                'ping_role_rejected' => 'nullable|string|max:50',
                'ping_role_fulfilled' => 'nullable|string|max:50',
                'enabled' => 'required|boolean',
            ]);
    
            // Remove empty role IDs
            foreach (['ping_role_created', 'ping_role_approved', 'ping_role_rejected', 'ping_role_fulfilled'] as $field) {
                if (isset($validated[$field]) && empty(trim($validated[$field]))) {
                    $validated[$field] = null;
                }
            }
    
            $config->update($validated);
    
            return response()->json([
                'success' => true,
                'message' => 'Webhook configuration updated successfully',
                'config' => $config->load('corporation')
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update webhook configuration: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Delete webhook configuration
     */
    public function deleteWebhookConfig($id)
    {
        try {
            $config = \BlueprintManager\Models\WebhookConfig::findOrFail($id);
            $config->delete();

            return response()->json([
                'success' => true,
                'message' => 'Webhook configuration deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete webhook configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test webhook
     */
    public function testWebhook(Request $request)
    {
        try {
            $validated = $request->validate([
                'webhook_url' => 'required|url',
            ]);

            $response = \Illuminate\Support\Facades\Http::timeout(10)->post($validated['webhook_url'], [
                'embeds' => [[
                    'title' => '🧪 Blueprint Manager Webhook Test',
                    'description' => 'This is a test notification from Blueprint Manager. If you see this, your webhook is configured correctly!',
                    'color' => 3447003,
                    'timestamp' => now()->toIso8601String(),
                    'footer' => [
                        'text' => 'Blueprint Manager'
                    ]
                ]]
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Webhook test failed: ' . $response->status() . ' - ' . $response->body()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

