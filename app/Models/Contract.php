<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits, SoftDeletes;

    protected $guarded = [];

    public const CONTRACT_STATUS = [
        'in_progress' => [
            'original' => 'in_progress',
            'converted' => 'Đang thực hiện',
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Hoàn thành',
        ],
    ];

    public const FINANCIAL_STATUS = self::CONTRACT_STATUS;

    public const INTERMEDIATE_PRODUCT_STATUS = [
        'in_progress' => [
            'original' => 'in_progress',
            'converted' => 'Đang thực hiện',
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Hoàn thành',
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

    public function instructors()
    {
        return $this->hasMany(ContractIntructor::class);
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

    public function manyYears()
    {
        return $this->hasMany(ContractManyYear::class);
    }

    public function extensions()
    {
        return $this->hasMany(ContractExtension::class);
    }

    public function scopes()
    {
        return $this->hasMany(ContractScope::class);
    }

    public function professionals()
    {
        return $this->hasMany(ContractProfessionals::class);
    }

    public function disbursements()
    {
        return $this->hasMany(ContractDisbursement::class);
    }

    public function intermediateCollaborators()
    {
        return $this->hasMany(ContractIntermediateCollaborators::class);
    }

    public function bills()
    {
        return $this->hasMany(ContractBill::class);
    }
}
