<?php

namespace BlueprintManager\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

/**
 * Per-corporation library visibility.
 *
 * One row per owning corporation controls who, beyond its own members, may
 * view its blueprint library and submit requests against it. A corporation
 * with no row stays private to its own members (the historical default).
 */
class BlueprintLibraryVisibility extends Model
{
    protected $table = 'blueprint_library_visibility';

    /** Private: only the owning corporation's own members (default). */
    public const MODE_CORP = 'corp';

    /** Shared with an explicit allowlist of corporations. */
    public const MODE_CORPORATIONS = 'corporations';

    /** Shared with every corporation in the owning corporation's alliance. */
    public const MODE_ALLIANCE = 'alliance';

    /** Shared with everyone who can access the plugin. */
    public const MODE_ALL = 'all';

    public const MODES = [
        self::MODE_CORP,
        self::MODE_CORPORATIONS,
        self::MODE_ALLIANCE,
        self::MODE_ALL,
    ];

    protected $fillable = [
        'corporation_id',
        'visibility_mode',
        'shared_corporation_ids',
    ];

    protected $casts = [
        'corporation_id' => 'integer',
        'shared_corporation_ids' => 'array',
    ];

    /**
     * The owning corporation.
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id');
    }
}
