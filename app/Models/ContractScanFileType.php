<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractScanFileType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extensions()
    {
        return $this->hasMany(ContractScanFileTypeExtension::class, 'type_id');
    }
}
