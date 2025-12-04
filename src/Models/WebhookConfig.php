<?php

namespace BlueprintManager\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Corporation\CorporationInfo;

class WebhookConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blueprint_webhook_configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'webhook_url',
        'corporation_id',
        'notify_created',
        'notify_approved',
        'notify_rejected',
        'notify_fulfilled',
        'ping_role_created',
        'ping_role_approved',
        'ping_role_rejected',
        'ping_role_fulfilled',
        'enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'notify_created' => 'boolean',
        'notify_approved' => 'boolean',
        'notify_rejected' => 'boolean',
        'notify_fulfilled' => 'boolean',
        'enabled' => 'boolean',
    ];

    /**
     * Get the corporation associated with this webhook
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id');
    }
}
