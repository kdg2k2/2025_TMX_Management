<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected $casts = [
        'before_adjust' => 'array',
    ];

    protected const TYPE = [
        'both' => [
            'original' => 'both',
            'converted' => 'Cả ngày',
        ],
        'morning' => [
            'original' => 'morning',
            'converted' => 'Sáng',
        ],
        'afternoon' => [
            'original' => 'afternoon',
            'converted' => 'Chiều',
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

    protected const ADJUST_APPROVAL_STATUS = [
        'none' => [
            'original' => 'none',
            'converted' => 'Không',
            'color' => 'outline-light border',
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

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPE, $key);
    }

    public function getApprovalStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::APPROVAL_STATUS, $key);
    }

    public function getAdjustApprovalStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::ADJUST_APPROVAL_STATUS, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function adjustApprovedBy()
    {
        return $this->belongsTo(User::class, 'adjust_approved_by');
    }
}
