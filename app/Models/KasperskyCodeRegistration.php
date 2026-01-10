<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KasperskyCodeRegistration extends Model
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
    ];

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
