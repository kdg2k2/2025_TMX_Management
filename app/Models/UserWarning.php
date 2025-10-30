<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWarning extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'detail' => 'array',
    ];

    protected const TYPE = [
        'job' => [
            'original' => 'job',
            'converted' => 'Công việc',
        ],
        'work_schedule' => [
            'original' => 'work_schedule',
            'converted' => 'Công tác',
        ],
    ];

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPE, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
