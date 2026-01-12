<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentContractPersonnel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employmentContractPersonnelPivotEmploymentContractPersonnelCustomField()
    {
        return $this->hasMany(EmploymentContractPersonnelPivotEmploymentContractPersonnelCustomField::class);
    }
}
