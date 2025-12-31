<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'ready' => [
            'original' => 'ready',
            'converted' => 'Sẵn sàng',
            'color' => 'success',
            'icon' => 'ti ti-circle-check',
        ],
        'loaned' => [
            'original' => 'loaned',
            'converted' => 'Cho Mượn',
            'color' => 'warning',
            'icon' => 'ti ti-arrow-forward-up',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }
}
