<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
