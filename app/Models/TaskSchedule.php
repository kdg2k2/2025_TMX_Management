<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSchedule extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

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
