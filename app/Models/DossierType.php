<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierType extends Model
{
    protected $guarded = [];

    /**
     * Get all plan details for this dossier type.
     */
    public function planDetails()
    {
        return $this->hasMany(DossierPlanDetail::class);
    }

    /**
     * Get all handover details for this dossier type.
     */
    public function handoverDetails()
    {
        return $this->hasMany(DossierHandoverDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all usage register details for this dossier type.
     */
    public function usageRegisterDetails()
    {
        return $this->hasMany(DossierUsageRegisterDetail::class);
    }
}
