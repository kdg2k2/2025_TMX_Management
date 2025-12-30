<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'normal' => [
            'original' => 'normal',
            'converted' => 'Bình Thường',
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
        'under_repair' => [
            'original' => 'under_repair',
            'converted' => 'Sửa Chữa',
            'color' => 'purple',
            'icon' => 'ti ti-tools',
        ],
        'stored' => [
            'original' => 'stored',
            'converted' => 'Lưu Kho',
            'color' => 'secondary',
            'icon' => 'ti ti-package',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(DeviceImage::class);
    }
}
