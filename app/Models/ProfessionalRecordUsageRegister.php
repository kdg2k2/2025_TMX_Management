<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordUsageRegister extends Model
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
        return $this->hasMany(ProfessionalRecordUsageRegisterDetail::class);
    }

    /**
     * Get the minutes (biên bản) for this usage register.
     */
    public function minutes()
    {
        return $this->hasMany(ProfessionalRecordMinute::class);
    }

    public function plan()
    {
        return $this->belongsTo(ProfessionalRecordPlan::class, 'professional_record_plan_id');
    }
}
