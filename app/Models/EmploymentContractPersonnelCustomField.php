<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentContractPersonnelCustomField extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPES = [
        'text' => [
            'original' => 'text',
            'converted' => 'Text/Float',
        ],
        'date' => [
            'original' => 'date',
            'converted' => 'Date',
        ],
        'number' => [
            'original' => 'number',
            'converted' => 'Integer',
        ],
    ];

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPES, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
