<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildSoftware extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const DEVELOPMENT_CASE = [
        'update' => [
            'original' => 'update',
            'converted' => 'Cập nhật, chỉnh sửa, bổ sung tính năng trên các hệ thống đã được xây dựng',
        ],
        'new' => [
            'original' => 'new',
            'converted' => 'Xây dựng hệ thống/phần mềm/công cụ hoàn toàn mới',
        ],
        'suddenly' => [
            'original' => 'suddenly',
            'converted' => 'Trường hợp phát sinh đột xuất',
        ],
    ];

    protected const STATE = [
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chưa thực hiện',
            'color' => 'primary',
        ],
        'doing_business_analysis' => [
            'original' => 'doing_business_analysis',
            'converted' => 'Đang phân tích nghiệp vụ',
            'color' => 'secondary',
        ],
        'construction_planning' => [
            'original' => 'construction_planning',
            'converted' => 'Đang lên kế hoạch xây dựng',
            'color' => 'warning',
        ],
        'in_progress' => [
            'original' => 'in_progress',
            'converted' => 'Đang thực hiện',
            'color' => 'danger',
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Hoàn thành',
            'color' => 'success',
        ],
    ];

    protected const STATUS = [
        'pending' => [
            'original' => 'pending',
            'converted' => 'Chờ duyệt',
            'color' => 'outline-primary',
        ],
        'accepted' => [
            'original' => 'accepted',
            'converted' => 'Đã duyệt',
            'color' => 'outline-success',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Từ chối',
            'color' => 'outline-danger',
        ],
    ];

    public function getDevelopmentCase($key = null)
    {
        return $this->getValueFromArrayByKey(self::DEVELOPMENT_CASE, $key);
    }

    public function getState($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATE, $key);
    }

    public function getStatus($key = null)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifyBy()
    {
        return $this->belongsTo(User::class, 'verify_by');
    }

    public function businessAnalysts()
    {
        return $this->hasMany(BuildSoftwareBusinessAnalyst::class, 'build_software_id');
    }

    public function members()
    {
        return $this->hasMany(BuildSoftwareMember::class, 'build_software_id');
    }
}
