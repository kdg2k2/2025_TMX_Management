<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPE_PROGRAM = [
        'contract' => [
            'original' => 'contract',
            'converted' => 'Hợp đồng',
        ],
        'other' => [
            'original' => 'other',
            'converted' => 'Khác',
        ],
    ];

    protected const APPROVAL_STATUS = [
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chờ duyệt',
            'color' => 'warning',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã duyệt',
            'color' => 'success',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Từ chối',
            'color' => 'danger',
        ],
    ];

    protected const END_APPROVAL_STATUS = [
        'none' => [
            'original' => 'none',
            'converted' => 'Không',
            'color' => 'light',
        ],
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chờ duyệt',
            'color' => 'warning',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã duyệt',
            'color' => 'success',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Từ chối',
            'color' => 'danger',
        ],
    ];

    protected const IS_COMPLETED = [
        '0' => [
            'original' => 0,
            'converted' => 'Đang thực hiện',
            'color' => 'warning',
        ],
        '1' => [
            'original' => 1,
            'converted' => 'Đã kết thúc',
            'color' => 'success',
        ],
    ];

    public function getTypeProgram($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPE_PROGRAM, $key);
    }

    public function getApprovalStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::APPROVAL_STATUS, $key);
    }

    public function getReturnApprovalStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::END_APPROVAL_STATUS, $key);
    }

    public function getIsCompleted($key = null)
    {
        return $this->getValueFromArrayByKey(self::IS_COMPLETED, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function endApprovedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
