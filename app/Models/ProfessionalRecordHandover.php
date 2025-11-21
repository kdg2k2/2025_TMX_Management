<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordHandover extends Model
{
    protected $guarded = [];

    /**
     * Get the plan that owns this handover.
     */
    public function plan()
    {
        return $this->belongsTo(ProfessionalRecordPlan::class, 'professional_record_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who hands over.
     */
    public function handoverBy()
    {
        return $this->belongsTo(User::class, 'handover_by');
    }

    /**
     * Get the user who receives.
     */
    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the handover details for this handover.
     */
    public function details()
    {
        return $this->hasMany(ProfessionalRecordHandoverDetail::class);
    }

    /**
     * Get the minutes (biên bản) for this handover.
     */
    public function minutes()
    {
        return $this->hasMany(ProfessionalRecordMinute::class);
    }
}
