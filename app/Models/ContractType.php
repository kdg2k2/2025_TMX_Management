<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extensions()
    {
        return $this->hasMany(ContractFileTypeExtension::class, 'type_id');
    }
}
