<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordUsageRegisterDetail extends Model
{
    protected $guarded = [];

    /**
     * Get the usage register that owns this detail.
     */
    public function usageRegister()
    {
        return $this->belongsTo(ProfessionalRecordUsageRegister::class, 'professional_record_usage_register_id');
    }

    /**
     * Get the professional_record type for this detail.
     */
    public function type()
    {
        return $this->belongsTo(ProfessionalRecordType::class, 'professional_record_type_id', 'id');
    }

    /**
     * Get the province for this detail.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    /**
     * Get the commune for this detail.
     */
    public function commune()
    {
        return $this->belongsTo(Commune::class, 'commune_code', 'code');
    }

    /**
     * Get the unit for this detail.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
