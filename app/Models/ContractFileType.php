<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractFileType extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPES = [
        'url' => [
            'original' => 'url',
            'converted' => 'Url',
        ],
        'file' => [
            'original' => 'file',
            'converted' => 'File',
        ],
    ];

    public function getTypes($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPES, $key);
    }

    public function extensions()
    {
        return $this->hasMany(ContractFileTypeExtension::class, 'type_id');
    }
}
