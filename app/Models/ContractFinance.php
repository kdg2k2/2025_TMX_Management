<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractFinance extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const ROLES = [
        'head_of_the_joint_venture' => [
            'converted' => 'Đứng đầu liên danh',
            'original' => 'head_of_the_joint_venture',
        ],
        'joint_venture_members' => [
            'converted' => 'Thành viên liên danh',
            'original' => 'joint_venture_members',
        ],
        'subcontractors' => [
            'converted' => 'Thầu phụ',
            'original' => 'subcontractors',
        ],
    ];

    public function getRole($key = null)
    {
        return $this->getValueFromArrayByKey(self::ROLES, $key);
    }

    public function contractUnit()
    {
        return $this->belongsTo(ContractUnit::class);
    }

    public function advancePayment()
    {
        return $this->hasMany(ContractAdvancePayment::class);
    }

    public function payment()
    {
        return $this->hasMany(ContractPayment::class);
    }
}
