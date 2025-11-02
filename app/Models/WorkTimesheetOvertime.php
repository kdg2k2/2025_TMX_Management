<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimesheetOvertime extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workTimesheet()
    {
        return $this->belongsTo(WorkTimesheet::class, 'work_timesheet_id');
    }

    public function details()
    {
        return $this->hasMany(WorkTimesheetOvertimeDetail::class, 'work_timesheet_overtime_id');
    }
}
