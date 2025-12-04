<?php

namespace BlueprintManager\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintContainerConfig;
use BlueprintManager\Models\BlueprintDetectionSettings;
use Seat\Eveapi\Models\Assets\CorporationAsset;
use Seat\Eveapi\Models\Corporation\CorporationBlueprint;
use Seat\Eveapi\Models\Sde\InvType;

class BlueprintService
{
    /**
     * Get blueprints for a corporation organized by configured categories
     *
     * CRITICAL: Blueprints ARE assets! We must join through the asset table.
     * blueprint.item_id = asset.item_id
     * asset.location_id = container.item_id
     *
     * @param int $corporationId
     * @param string|null $category
     * @return Collection
     */
    public function getBlueprintsByCategory(int $corporationId, ?string $category = null): Collection
    {
        $configs = BlueprintContainerConfig::getEnabledForCorporation($corporationId);

        if ($configs->isEmpty()) {
            return collect();
        }

        // Get all container item IDs that match our configurations
        $containerMatches = $this->getMatchingContainers($corporationId, $configs);

        if ($containerMatches->isEmpty()) {
            return collect();
        }

        $containerIds = $containerMatches->pluck('item_id');

        // Filter by category if specified
        if ($category) {
            $categoryContainers = $containerMatches->where('display_category', $category);
            $containerIds = $categoryContainers->pluck('item_id');
        }

        // CORRECTED JOIN LOGIC:
        // 1. Find blueprint assets WHERE asset.location_id = container.item_id
        $blueprintItemIds = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('location_id', $containerIds)
            ->pluck('item_id');

        if ($blueprintItemIds->isEmpty()) {
            return collect();
        }

        // 2. Get blueprint records WHERE blueprint.item_id = asset.item_id
        $blueprints = CorporationBlueprint::where('corporation_id', $corporationId)
            ->whereIn('item_id', $blueprintItemIds)
            ->with(['type'])
            ->get();

        // 3. Add container information by looking up the asset's location
        $blueprints = $blueprints->map(function ($blueprint) use ($corporationId, $containerMatches) {
            // Find the asset for this blueprint
            $asset = CorporationAsset::where('corporation_id', $corporationId)
                ->where('item_id', $blueprint->item_id)
                ->first();

            if ($asset) {
                // The asset's location_id is the container
                $container = $containerMatches->firstWhere('item_id', $asset->location_id);
                $blueprint->container_name = $container->container_name ?? 'Unknown';
                $blueprint->display_category = $container->display_category ?? 'Unknown';
            } else {
                $blueprint->container_name = 'Unknown';
                $blueprint->display_category = 'Unknown';
            }

            $blueprint->is_bpo = $blueprint->runs === -1;
            return $blueprint;
        });

        // Group by type_id and aggregate
        $grouped = $blueprints->groupBy('type_id')->map(function ($group) {
            $first = $group->first();
            
            return (object) [
                'type_id' => $first->type_id,
                'type_name' => $first->type->typeName ?? 'Unknown',
                'category' => $first->display_category,
                'is_bpo' => $first->is_bpo,
                'quantity' => $group->count(),
                'min_me' => $group->min('material_efficiency'),
                'max_me' => $group->max('material_efficiency'),
                'avg_me' => round($group->avg('material_efficiency'), 1),
                'min_te' => $group->min('time_efficiency'),
                'max_te' => $group->max('time_efficiency'),
                'avg_te' => round($group->avg('time_efficiency'), 1),
                'runs' => $first->is_bpo ? null : $group->pluck('runs')->unique()->sort()->values(),
                'locations' => $group->pluck('item_id')->unique()->count(),
                'blueprints' => $group, // Include individual blueprints for detail view
            ];
        })->values();

        return $grouped;
    }

    /**
     * Get matching containers based on configurations
     *
     * @param int $corporationId
     * @param Collection $configs
     * @return Collection
     */
    private function getMatchingContainers(int $corporationId, Collection $configs): Collection
    {
        $containers = CorporationAsset::where('corporation_id', $corporationId)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->get();

        $matches = collect();

        foreach ($containers as $container) {
            foreach ($configs as $config) {
                if ($config->matches($container->name)) {
                    $matches->push((object) [
                        'item_id' => $container->item_id,
                        'container_name' => $container->name,
                        'display_category' => $config->display_category,
                        'location_id' => $container->location_id,
                    ]);
                    break; // Only match first config per container
                }
            }
        }

        return $matches;
    }

    /**
     * Get all unique categories configured for a corporation
     *
     * @param int $corporationId
     * @return Collection
     */
    public function getCategoriesForCorporation(int $corporationId): Collection
    {
        return BlueprintContainerConfig::where('corporation_id', $corporationId)
            ->where('enabled', true)
            ->select('display_category')
            ->distinct()
            ->orderBy('display_category')
            ->pluck('display_category');
    }

    /**
     * Get blueprint details including location information
     *
     * CRITICAL: Blueprints ARE assets! We must join through the asset table.
     *
     * @param int $corporationId
     * @param int $typeId
     * @return Collection
     */
    public function getBlueprintDetails(int $corporationId, int $typeId): Collection
    {
        $configs = BlueprintContainerConfig::getEnabledForCorporation($corporationId);
        $containerMatches = $this->getMatchingContainers($corporationId, $configs);

        if ($containerMatches->isEmpty()) {
            return collect();
        }

        $containerIds = $containerMatches->pluck('item_id');

        // CORRECTED JOIN LOGIC:
        // 1. Find blueprint assets WHERE asset.location_id = container.item_id
        $blueprintItemIds = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('location_id', $containerIds)
            ->pluck('item_id');

        if ($blueprintItemIds->isEmpty()) {
            return collect();
        }

        // 2. Get blueprint records WHERE blueprint.item_id = asset.item_id AND type matches
        return CorporationBlueprint::where('corporation_id', $corporationId)
            ->where('type_id', $typeId)
            ->whereIn('item_id', $blueprintItemIds)
            ->with(['type'])
            ->get()
            ->map(function ($blueprint) use ($corporationId, $containerMatches) {
                // Find the asset for this blueprint
                $asset = CorporationAsset::where('corporation_id', $corporationId)
                    ->where('item_id', $blueprint->item_id)
                    ->first();

                if ($asset) {
                    // The asset's location_id is the container
                    $container = $containerMatches->firstWhere('item_id', $asset->location_id);
                    $blueprint->container_name = $container->container_name ?? 'Unknown';
                    $blueprint->display_category = $container->display_category ?? 'Unknown';
                } else {
                    $blueprint->container_name = 'Unknown';
                    $blueprint->display_category = 'Unknown';
                }

                return $blueprint;
            });
    }

    /**
     * Detect all blueprint containers for a corporation with station information
     * 
     * KEY INSIGHT: Blueprints ARE assets! The blueprint's item_id matches an asset's item_id.
     * The asset's location_id then points to the container.
     *
     * @param int $corporationId
     * @param array|null $hangarFilter Optional array of hangar divisions to scan (e.g., ['CorpSAG6', 'CorpSAG7'])
     * @return Collection Returns collection of objects with container_name and station_name
     */
    public function detectBlueprintContainers(int $corporationId, ?array $hangarFilter = null): Collection
    {
        // Type IDs for Upwell structures
        $upwellStructureTypes = [
            35825, // Raitaru
            35826, // Azbel
            35827, // Sotiyo
            35832, // Astrahus
            35833, // Fortizar
            35834, // Keepstar
            35835, // Athanor
            35836, // Tatara
        ];

        // Build blueprint query
        $blueprintQuery = CorporationBlueprint::where('corporation_id', $corporationId);

        // Apply hangar filter if provided
        if ($hangarFilter !== null && !empty($hangarFilter)) {
            $blueprintQuery->whereIn('location_flag', $hangarFilter);
        }

        // Get all blueprint item_ids (blueprints ARE assets!)
        $blueprintItemIds = $blueprintQuery->pluck('item_id')->unique();

        if ($blueprintItemIds->isEmpty()) {
            return collect();
        }

        // Find the asset records for these blueprints
        // The asset's location_id tells us which container they're in
        $blueprintAssets = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('item_id', $blueprintItemIds)
            ->get();

        // Get unique container location_ids (where the blueprint assets are located)
        $containerIds = $blueprintAssets->pluck('location_id')->unique();

        // Get the container details
        $containers = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('item_id', $containerIds)
            ->whereNotIn('type_id', $upwellStructureTypes)  // Exclude structures
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->get();

        // Get parent station information for each container
        $stationIds = $containers->pluck('location_id')->unique();
        $stations = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('item_id', $stationIds)
            ->get()
            ->keyBy('item_id');

        $results = collect();

        foreach ($containers as $container) {
            $station = $stations->get($container->location_id);
            
            // Determine station name
            $stationName = 'Unknown Station';
            if ($station) {
                if (in_array($station->type_id, $upwellStructureTypes)) {
                    // Direct parent is a structure
                    $stationName = $station->name;
                } elseif ($station->type_id == 27) {
                    // Parent is an Office (type_id 27), look up the office's parent structure
                    $parentStation = CorporationAsset::where('corporation_id', $corporationId)
                        ->where('item_id', $station->location_id)
                        ->first();
                    if ($parentStation) {
                        $stationName = $parentStation->name ?? 'Unknown Station';
                    }
                }
            }

            $results->push((object) [
                'container_name' => $container->name,
                'station_name' => $stationName,
                'location_id' => $container->location_id,
            ]);
        }

        // Sort by station name, then by container name
        return $results->sortBy([
            ['station_name', 'asc'],
            ['container_name', 'asc'],
        ])->values();
    }

    /**
     * Get blueprint count by category for a corporation
     *
     * @param int $corporationId
     * @return Collection
     */
    public function getBlueprintCountsByCategory(int $corporationId): Collection
    {
        $categories = $this->getCategoriesForCorporation($corporationId);
        
        return $categories->mapWithKeys(function ($category) use ($corporationId) {
            $blueprints = $this->getBlueprintsByCategory($corporationId, $category);
            return [$category => $blueprints->count()];
        });
    }
}
