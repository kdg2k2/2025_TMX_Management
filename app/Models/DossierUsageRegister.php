<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierUsageRegister extends Model
{
    protected $guarded = [];

    /**
     * Get the user who registered this usage.
     */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get the usage register details for this register.
     */
    public function details()
    {
        return $this->hasMany(DossierUsageRegisterDetail::class);
    }

    /**
     * Get the minutes (biên bản) for this usage register.
     */
    public function minutes()
    {
        return $this->hasMany(DossierMinute::class);
    }

    public function plan()
    {
        return $this->belongsTo(DossierPlan::class, 'dossier_plan_id');
    }
}
