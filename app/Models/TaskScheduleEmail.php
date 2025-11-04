<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskScheduleEmail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function taskSchedule()
    {
        return $this->belongsTo(TaskSchedule::class);
    }
}
