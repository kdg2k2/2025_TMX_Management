<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractType extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function extensions()
    {
        return $this->hasMany(ContractFileTypeExtension::class, 'type_id');
    }
}
