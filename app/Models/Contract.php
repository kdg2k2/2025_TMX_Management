<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    public const CONTRACT_STATUS = [
        'in_progress' => [
            'original' => 'Đang thực hiện',
            'converted' => 'in_progress',
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Hoàn thành',
        ],
    ];

    public const FINANCIAL_STATUS = [
        'in_progress' => [
            'original' => 'Đang thực hiện',
            'converted' => 'in_progress',
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Hoàn thành',
        ],
    ];

    public const INTERMEDIATE_PRODUCT_STATUS = [
        'completed' => [
            'original' => 'completed',
            'converted' => 'Đã hoàn thành',
        ],
        'in_progress' => [
            'original' => 'in_progress',
            'converted' => 'Đang thực hiện',
        ],
        'pending_review' => [
            'original' => 'pending_review',
            'converted' => 'Đề nghị kiểm tra',
        ],
        'multi_year' => [
            'original' => 'multi_year',
            'converted' => 'Thực hiện nhiều năm',
        ],
        'technical_done' => [
            'original' => 'technical_done',
            'converted' => 'Đã xong kỹ thuật',
        ],
        'has_issues' => [
            'original' => 'has_issues',
            'converted' => 'Còn tồn tại',
        ],
        'issues_recorded' => [
            'original' => 'issues_recorded',
            'converted' => 'Ghi nhận tồn tại',
        ],
    ];

    public function getContractStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::CONTRACT_STATUS, $key);
    }

    public function getFinancialStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::FINANCIAL_STATUS, $key);
    }

    public function getIntermediateProductStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::INTERMEDIATE_PRODUCT_STATUS, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function accountingContact()
    {
        return $this->belongsTo(User::class, 'accounting_contact_id');
    }

    public function inspectorUser()
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }

    public function executorUser()
    {
        return $this->belongsTo(User::class, 'executor_user_id');
    }

    public function type()
    {
        return $this->belongsTo(ContractType::class, 'type_id');
    }

    public function investor()
    {
        return $this->belongsTo(ContractInvestor::class, 'investor_id');
    }
}
