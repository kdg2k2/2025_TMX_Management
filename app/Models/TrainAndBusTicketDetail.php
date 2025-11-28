<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainAndBusTicketDetail extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const USER_TYPES = [
        'internal' => [
            'original' => 'internal',
            'converted' => 'Nội bộ',
        ],
        'external' => [
            'original' => 'external',
            'converted' => 'Bên ngoài',
        ],
    ];

    public function getUserType($key = null)
    {
        return $this->getValueFromArrayByKey(self::USER_TYPES, $key);
    }

    public function trainAndBusTicket()
    {
        return $this->belongsTo(TrainAndBusTicket::class);
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
