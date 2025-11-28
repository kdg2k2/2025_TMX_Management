<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainAndBusTicket extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const TYPES = [
        'contract' => [
            'original' => 'contract',
            'converted' => 'Theo hợp đồng',
        ],
        'other' => [
            'original' => 'other',
            'converted' => 'Chương trình khác',
        ],
    ];

    protected const STATUS = [
        'pending_approval' => [
            'original' => 'pending_approval',
            'converted' => 'Chờ phê duyệt',
            'color' => 'warning',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã phê duyệt',
            'color' => 'success',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Đã từ chối',
            'color' => 'danger',
        ],
    ];

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPES, $key);
    }

    public function getStatus($key)
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

    public function details()
    {
        return $this->hasMany(TrainAndBusTicketDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
