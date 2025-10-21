<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelFileType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extensions()
    {
        return $this->hasMany(PersonnelFileTypeExtension::class, 'type_id');
    }
}
