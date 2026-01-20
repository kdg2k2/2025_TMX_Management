<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductMinuteSignature extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPE = [
        'draw' => [
            'original' => 'draw',
            'converted' => 'Ký tay',
            'color' => 'info',
            'icon' => 'ti ti-pencil',
        ],
        'text' => [
            'original' => 'text',
            'converted' => 'Nhập text',
            'color' => 'secondary',
            'icon' => 'ti ti-typography',
        ],
        'upload' => [
            'original' => 'upload',
            'converted' => 'Chọn ảnh',
            'color' => 'warning',
            'icon' => 'ti ti-upload',
        ],
        'profile' => [
            'original' => 'profile',
            'converted' => 'Dùng chữ ký cá nhân',
            'color' => 'primary',
            'icon' => 'ti ti-id-badge',
        ],
    ];

    protected const STATUS = [
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chờ ký',
            'color' => 'warning',
            'icon' => 'ti ti-clock-hour-4',
        ],
        'signed' => [
            'original' => 'signed',
            'converted' => 'Đã ký',
            'color' => 'success',
            'icon' => 'ti ti-circle-check',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPE, $key);
    }

    public function minute()
    {
        return $this->belongsTo(ContractProductMinute::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
