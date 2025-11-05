<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierPlan extends Model
{
    protected $guarded = [];

    /**
     * Get the contract that owns this plan.
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    /**
     * Get the user that created this plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handoverBy()
    {
        return $this->belongsTo(User::class, 'handover_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the plan details for this plan.
     */
    public function details()
    {
        return $this->hasMany(DossierPlanDetail::class);
    }

    /**
     * Get all handovers for this plan.
     */
    public function handovers()
    {
        return $this->hasMany(DossierHandover::class);
    }

    /**
     * Get the minutes (biên bản) for this plan.
     */
    public function minutes()
    {
        return $this->hasMany(DossierMinute::class);
    }
}
