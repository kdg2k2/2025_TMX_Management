<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function personnelUnit()
    {
        return $this->belongsTo(PersonnelUnit::class);
    }
}
