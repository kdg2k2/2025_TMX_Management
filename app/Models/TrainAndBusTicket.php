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
        'orther' => [
            'original' => 'orther',
            'converted' => 'Khác',
        ],
    ];

    public function getType($key = null)
    {
        return $this->getValueFromArrayByKey(self::TYPES, $key);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
