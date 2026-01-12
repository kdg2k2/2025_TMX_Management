<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected const TYPE = [
        'personal' => [
            'original' => 'personal',
            'converted' => 'Máy cá nhân',
            'color' => 'secondary',
            'icon' => 'ti ti-user',
        ],
        'company' => [
            'original' => 'company',
            'converted' => 'Thiết bị công ty',
            'color' => 'primary',
            'icon' => 'ti ti-device-desktop',
        ],
        'both' => [
            'original' => 'both',
            'converted' => 'Máy cá nhân & thiết bị công ty',
            'color' => 'info',
            'icon' => 'ti ti-devices',
        ],
    ];

    public function getType($key)
    {
        return $this->getValueFromArrayByKey(self::TYPE, $key);
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

    public function codes()
    {
        return $this->belongsToMany(KasperskyCode::class, KasperskyCodeRegistrationItem::class);
    }
}
