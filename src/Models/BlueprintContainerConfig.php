<?php

namespace BlueprintManager\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

class BlueprintContainerConfig extends Model
{
    protected $table = 'blueprint_container_configs';

    protected $fillable = [
        'corporation_id',
        'container_name',
        'display_category',
        'match_type',
        'enabled',
        'priority',
    ];

    protected $casts = [
        'corporation_id' => 'integer',
        'enabled' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Corporation relationship
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id');
    }

    /**
     * Check if a container name matches this config
     *
     * @param string $containerName
     * @return bool
     */
    public function matches(string $containerName): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return match($this->match_type) {
            'exact' => $containerName === $this->container_name,
            'contains' => str_contains($containerName, $this->container_name),
            'starts_with' => str_starts_with($containerName, $this->container_name),
            default => false,
        };
    }

    /**
     * Get all enabled configs for a corporation, ordered by priority
     *
     * @param int $corporationId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getEnabledForCorporation(int $corporationId)
    {
        return static::where('corporation_id', $corporationId)
            ->where('enabled', true)
            ->orderBy('priority', 'desc')
            ->orderBy('display_category')
            ->get();
    }
}
