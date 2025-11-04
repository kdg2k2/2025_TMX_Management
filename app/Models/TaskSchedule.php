<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSchedule extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    protected const FREQUENCY = [
        'daily' => [
            'original' => 'daily',
            'converted' => 'Hàng ngày',
        ],
        'weekly' => [
            'original' => 'weekly',
            'converted' => 'Hàng tuần',
        ],
        'monthly' => [
            'original' => 'monthly',
            'converted' => 'Hàng tháng',
        ],
    ];

    public function getFrequency($key = null)
    {
        return $this->getValueFromArrayByKey(self::FREQUENCY, $key);
    }

    public function emails()
    {
        return $this->hasMany(TaskScheduleEmail::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'task_schedule_emails',
            'task_schedule_id',
            'user_id'
        )->withTimestamps();
    }
}
