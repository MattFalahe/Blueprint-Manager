<?php

namespace BlueprintManager\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use BlueprintManager\Models\BlueprintContainerConfig;
use Seat\Eveapi\Models\Assets\CorporationAsset;
use Seat\Eveapi\Models\Corporation\CorporationBlueprint;
use Seat\Eveapi\Models\Sde\InvType;

class BlueprintService
{
    /**
     * Get blueprints for a corporation organized by configured categories
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

        // Build query for blueprints in these containers
        $query = CorporationBlueprint::where('corporation_id', $corporationId)
            ->whereIn('location_id', $containerMatches->pluck('item_id'));

        // Filter by category if specified
        if ($category) {
            $categoryContainers = $containerMatches->where('display_category', $category);
            $query->whereIn('location_id', $categoryContainers->pluck('item_id'));
        }

        // Get blueprints with type information
        $blueprints = $query->with(['type'])
            ->get();

        // Add container and category information
        $blueprints = $blueprints->map(function ($blueprint) use ($containerMatches) {
            $container = $containerMatches->firstWhere('item_id', $blueprint->location_id);
            $blueprint->container_name = $container->container_name ?? 'Unknown';
            $blueprint->display_category = $container->display_category ?? 'Unknown';
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
                'locations' => $group->pluck('location_id')->unique()->count(),
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
     * @param int $corporationId
     * @param int $typeId
     * @return Collection
     */
    public function getBlueprintDetails(int $corporationId, int $typeId): Collection
    {
        $configs = BlueprintContainerConfig::getEnabledForCorporation($corporationId);
        $containerMatches = $this->getMatchingContainers($corporationId, $configs);

        return CorporationBlueprint::where('corporation_id', $corporationId)
            ->where('type_id', $typeId)
            ->whereIn('location_id', $containerMatches->pluck('item_id'))
            ->with(['type'])
            ->get()
            ->map(function ($blueprint) use ($containerMatches) {
                $container = $containerMatches->firstWhere('item_id', $blueprint->location_id);
                $blueprint->container_name = $container->container_name ?? 'Unknown';
                $blueprint->display_category = $container->display_category ?? 'Unknown';
                return $blueprint;
            });
    }

    /**
     * Detect all blueprint containers for a corporation
     *
     * @param int $corporationId
     * @return Collection
     */
    public function detectBlueprintContainers(int $corporationId): Collection
    {
        // Get all named containers
        $containers = CorporationAsset::where('corporation_id', $corporationId)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->select('name')
            ->distinct()
            ->get();

        // Get all blueprints
        $blueprintLocations = CorporationBlueprint::where('corporation_id', $corporationId)
            ->pluck('location_id')
            ->unique();

        // Filter containers that contain blueprints
        $blueprintContainerIds = CorporationAsset::where('corporation_id', $corporationId)
            ->whereIn('item_id', $blueprintLocations)
            ->pluck('item_id');

        return $containers->filter(function ($container) use ($corporationId, $blueprintContainerIds) {
            $asset = CorporationAsset::where('corporation_id', $corporationId)
                ->where('name', $container->name)
                ->first();

            return $asset && $blueprintContainerIds->contains($asset->item_id);
        })->pluck('name')->unique()->sort()->values();
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
