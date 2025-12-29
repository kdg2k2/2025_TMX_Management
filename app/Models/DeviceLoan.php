<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceLoan extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chờ phê duyệt',
            'color' => 'warning',
            'icon' => 'ti ti-clock',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã duyệt',
            'color' => 'primary',
            'icon' => 'ti ti-check',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Từ chối',
            'color' => 'danger',
            'icon' => 'ti ti-x',
        ],
        'returned' => [
            'original' => 'returned',
            'converted' => 'Đã trả',
            'color' => 'success',
            'icon' => 'ti ti-refresh',
        ],
    ];

    protected const STATUS_RETURN = [
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
            'color' => 'warning',
            'icon' => 'ti ti-alert-circle',
        ],
        'lost' => [
            'original' => 'lost',
            'converted' => 'Thất Lạc',
            'color' => 'dark',
            'icon' => 'ti ti-help',
        ],
    ];

    public function getStatusReturn($key)
    {
        return $this->getValueFromArrayByKey(self::STATUS_RETURN, $key);
    }

    public function getStatus($key)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
