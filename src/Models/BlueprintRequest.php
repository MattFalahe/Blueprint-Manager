<?php

namespace BlueprintManager\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Sde\InvType;

class BlueprintRequest extends Model
{
    protected $table = 'blueprint_requests';

    protected $fillable = [
        'corporation_id',
        'character_id',
        'blueprint_type_id',
        'quantity',
        'runs',
        'status',
        'notes',
        'response_notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'fulfilled_by',
        'fulfilled_at',
    ];

    protected $casts = [
        'corporation_id' => 'integer',
        'character_id' => 'integer',
        'blueprint_type_id' => 'integer',
        'quantity' => 'integer',
        'runs' => 'integer',
        'approved_by' => 'integer',
        'rejected_by' => 'integer',
        'fulfilled_by' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    /**
     * Corporation relationship
     */
    public function corporation()
    {
        return $this->belongsTo(CorporationInfo::class, 'corporation_id', 'corporation_id');
    }

    /**
     * Character who made the request
     */
    public function character()
    {
        return $this->belongsTo(CharacterInfo::class, 'character_id', 'character_id');
    }

    /**
     * Character who approved the request
     */
    public function approver()
    {
        return $this->belongsTo(CharacterInfo::class, 'approved_by', 'character_id');
    }

    /**
     * Character who rejected the request
     */
    public function rejector()
    {
        return $this->belongsTo(CharacterInfo::class, 'rejected_by', 'character_id');
    }

    /**
     * Character who fulfilled the request
     */
    public function fulfiller()
    {
        return $this->belongsTo(CharacterInfo::class, 'fulfilled_by', 'character_id');
    }

    /**
     * Blueprint type information
     */
    public function blueprintType()
    {
        return $this->belongsTo(InvType::class, 'blueprint_type_id', 'typeID');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for fulfilled requests
     */
    public function scopeFulfilled($query)
    {
        return $query->where('status', 'fulfilled');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope for specific corporation
     */
    public function scopeForCorporation($query, int $corporationId)
    {
        return $query->where('corporation_id', $corporationId);
    }

    /**
     * Scope for specific character
     */
    public function scopeForCharacter($query, int $characterId)
    {
        return $query->where('character_id', $characterId);
    }

    /**
     * Approve the request
     */
    public function approve(int $approverId, ?string $notes = null)
    {
        $this->status = 'approved';
        $this->approved_by = $approverId;
        $this->approved_at = now();
        if ($notes) {
            $this->response_notes = $notes;
        }
        $this->save();
    }

    /**
     * Reject the request
     */
    public function reject(int $rejectorId, ?string $notes = null)
    {
        $this->status = 'rejected';
        $this->rejected_by = $rejectorId;
        $this->rejected_at = now();
        if ($notes) {
            $this->response_notes = $notes;
        }
        $this->save();
    }

    /**
     * Fulfill the request
     */
    public function fulfill(int $fulfillerId, ?string $notes = null)
    {
        $this->status = 'fulfilled';
        $this->fulfilled_by = $fulfillerId;
        $this->fulfilled_at = now();
        if ($notes) {
            $this->response_notes = $notes;
        }
        $this->save();
    }
}
