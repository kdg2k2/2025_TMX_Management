<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Model;

class ProfessionalRecordMinute extends Model
{
    use GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPES = [
        'plan' => [
            'original' => 'plan',
            'converted' => 'Biên bản kế hoạch',
            'color' => 'primary',
        ],
        'handover' => [
            'original' => 'handover',
            'converted' => 'Biên bản bàn giao',
            'color' => 'info',
        ],
        'usage_register' => [
            'original' => 'usage_register',
            'converted' => 'Biên bản đăng ký sử dụng',
            'color' => 'success',
        ],
    ];

    protected const STATUS = [
        'draft' => [
            'original' => 'draft',
            'converted' => 'Bản nháp',
            'color' => 'secondary',
        ],
        'pending_approval' => [
            'original' => 'pending_approval',
            'converted' => 'Chờ phê duyệt',
            'color' => 'warning',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã phê duyệt',
            'color' => 'success',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Đã từ chối',
            'color' => 'danger',
        ],
    ];

    public function getType($key)
    {
        return $this->getValueFromArrayByKey(self::TYPES, $key);
    }

    public function getStatus($key)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    /**
     * Get the plan that this minutes belongs to.
     */
    public function plan()
    {
        return $this->belongsTo(ProfessionalRecordPlan::class, 'professional_record_plan_id');
    }

    /**
     * Get the handover that this minutes belongs to.
     */
    public function handover()
    {
        return $this->belongsTo(ProfessionalRecordHandover::class, 'professional_record_handover_id');
    }

    /**
     * Get the usage register that this minutes belongs to.
     */
    public function usageRegister()
    {
        return $this->belongsTo(ProfessionalRecordUsageRegister::class, 'professional_record_usage_register_id');
    }

    /**
     * Get the user who approved this minutes.
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
