<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordType extends Model
{
    protected $guarded = [];

    /**
     * Get all plan details for this professional_record type.
     */
    public function planDetails()
    {
        return $this->hasMany(ProfessionalRecordPlanDetail::class);
    }

    /**
     * Get all handover details for this professional_record type.
     */
    public function handoverDetails()
    {
        return $this->hasMany(ProfessionalRecordHandoverDetail::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all usage register details for this professional_record type.
     */
    public function usageRegisterDetails()
    {
        return $this->hasMany(ProfessionalRecordUsageRegisterDetail::class);
    }
}
