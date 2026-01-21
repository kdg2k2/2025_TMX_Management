<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductInspection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected const STATUS = [
        'request' => [
            'original' => 'request',
            'converted' => 'Yêu cầu kiểm tra',
            'color' => 'warning',
            'icon' => 'ti ti-clock-hour-4',
        ],
        'responded' => [
            'original' => 'responded',
            'converted' => 'Đã phản hồi',
            'color' => 'success',
            'icon' => 'ti ti-circle-check',
        ],
        'cancel' => [
            'original' => 'cancel',
            'converted' => 'Hủy kiểm tra',
            'color' => 'danger',
            'icon' => 'ti ti-ban',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function years()
    {
        return $this->hasMany(ContractProductInspectionYear::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function inspectorUser()
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }
}
