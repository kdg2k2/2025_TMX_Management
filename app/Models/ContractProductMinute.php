<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractProductMinute extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'draft' => [
            'original' => 'draft',
            'converted' => 'Nháp',
            'color' => 'secondary',
            'icon' => 'ti ti-file-text',
        ],
        'request_sign' => [
            'original' => 'request_sign',
            'converted' => 'Yêu cầu ký',
            'color' => 'warning',
            'icon' => 'ti ti-signature',
        ],
        'request_approve' => [
            'original' => 'request_approve',
            'converted' => 'Yêu cầu duyệt',
            'color' => 'info',
            'icon' => 'ti ti-user-check',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã duyệt',
            'color' => 'success',
            'icon' => 'ti ti-circle-check',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Từ chối',
            'color' => 'danger',
            'icon' => 'ti ti-circle-x',
        ],
    ];

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function signatures()
    {
        return $this->hasMany(ContractProductMinuteSignature::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function contractProfessional()
    {
        return $this->belongsTo(ContractProfessionals::class);
    }
    
    public function contractDisbursement()
    {
        return $this->belongsTo(ContractDisbursement::class);
    }
}
