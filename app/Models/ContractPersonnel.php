<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractPersonnel extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const IS_IN_CONTRACT = [
        '1' => [
            'original' => 1,
            'converted' => 'Có',
            'color' => 'success',
            'icon' => 'ti ti-check',
        ],
        '0' => [
            'original' => 0,
            'converted' => 'Không',
            'color' => 'danger',
            'icon' => 'ti ti-ban',
        ],
    ];

    public function isInContract($key = null)
    {
        return $this->getValueFromArrayByKey(self::IS_IN_CONTRACT, $key);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }
}
