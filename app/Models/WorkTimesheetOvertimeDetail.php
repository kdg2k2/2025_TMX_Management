<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimesheetOvertimeDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'detail_leave_days_without_permission' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workTimesheetOvertime()
    {
        return $this->belongsTo(WorkTimesheetOvertime::class, 'work_timesheet_overtime_id');
    }
}
