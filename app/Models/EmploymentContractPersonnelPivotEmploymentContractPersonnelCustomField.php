<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentContractPersonnelPivotEmploymentContractPersonnelCustomField extends Model
{
    use HasFactory;

    protected $table = 'employment_contract_personnel_custom_fields_pivot';

    protected $guarded = [];

    public function employmentContractPersonnel()
    {
        return $this->belongsTo(EmploymentContractPersonnel::class);
    }

    public function employmentContractPersonnelCustomField()
    {
        return $this->belongsTo(EmploymentContractPersonnelCustomField::class);
    }
}
