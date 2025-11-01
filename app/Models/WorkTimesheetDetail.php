<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimesheetDetail extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'business_trip_days' => 'array',
    ];

    public function workTimeSheet()
    {
        return $this->belongsTo(WorkTimesheet::class, 'work_timesheet_id');
    }
}
