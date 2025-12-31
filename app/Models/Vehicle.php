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
        'unwashed' => [
            'original' => 'unwashed',
            'converted' => 'Chưa rửa',
            'color' => 'purple',
            'icon' => 'ti ti-car-crash',
        ],
        'ready' => [
            'original' => 'ready',
            'converted' => 'Sẵn sàng',
            'color' => 'success',
            'icon' => 'ti ti-circle-check',
        ],
        'broken' => [
            'original' => 'broken',
            'converted' => 'Hỏng',
            'color' => 'danger',
            'icon' => 'ti ti-x',
        ],
        'faulty' => [
            'original' => 'faulty',
            'converted' => 'Lỗi',
            'color' => 'orange',
            'icon' => 'ti ti-alert-circle',
        ],
        'lost' => [
            'original' => 'lost',
            'converted' => 'Thất Lạc',
            'color' => 'dark',
            'icon' => 'ti ti-help',
        ],
        'loaned' => [
            'original' => 'loaned',
            'converted' => 'Cho Mượn',
            'color' => 'teal',
            'icon' => 'ti ti-arrow-forward-up',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
