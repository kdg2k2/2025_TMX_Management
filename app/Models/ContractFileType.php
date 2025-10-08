<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractFileType extends Model
{
    use HasFactory;

    public function extensions()
    {
        return $this->hasMany(ContractFileTypeExtension::class, 'type_id');
    }
}
