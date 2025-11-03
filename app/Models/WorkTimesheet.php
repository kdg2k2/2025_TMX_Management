<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTimesheet extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'days_details' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(WorkTimesheetDetail::class, 'work_timesheet_id');
    }

    public function overtimes()
    {
        return $this->hasMany(WorkTimesheetOvertime::class, 'work_timesheet_id');
    }
}
