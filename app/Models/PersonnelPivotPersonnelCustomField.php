<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelPivotPersonnelCustomField extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function personnelCustomField()
    {
        return $this->belongsTo(PersonnelCustomField::class);
    }
}
