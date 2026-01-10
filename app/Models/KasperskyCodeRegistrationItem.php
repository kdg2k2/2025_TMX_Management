<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasperskyCodeRegistrationItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kasperskyCode()
    {
        return $this->belongsTo(KasperskyCode::class);
    }
}
