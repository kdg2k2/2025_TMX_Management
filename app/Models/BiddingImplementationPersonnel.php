<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingImplementationPersonnel extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const JOB_TITLES = [
        'project_manager' => [
            'original' => 'project_manager',
            'converted' => 'Chủ nhiệm dự án',
        ],
        'topic_leader' => [
            'original' => 'topic_leader',
            'converted' => 'Chủ trì chuyên đề',
        ],
        'expert' => [
            'original' => 'expert',
            'converted' => 'Chuyên gia',
        ],
        'support_staff' => [
            'original' => 'support_staff',
            'converted' => 'Cán bộ hỗ trợ',
        ],
    ];

    public function getJobTitle($key = null)
    {
        return $this->getValueFromArrayByKey(self::JOB_TITLES, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function files()
    {
        return $this->hasMany(BiddingImplementationPersonnelFile::class, 'bidding_implementation_personnel_id');
    }
}
