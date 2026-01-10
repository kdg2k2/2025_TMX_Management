<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasperskyCode extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const IS_QUANTITY_EXCEEDED = [
        'true' => [
            'original' => 'true',
            'converted' => 'Hết lượt sử dụng',
            'color' => 'danger',
            'icon' => 'ti ti-ban',
        ],
        'false' => [
            'original' => 'false',
            'converted' => 'Còn lượt sử dụng',
            'color' => 'success',
            'icon' => 'ti ti-check',
        ],
    ];

    protected const IS_EXPIRED = [
        'true' => [
            'original' => 'true',
            'converted' => 'Hết hạn',
            'color' => 'danger',
            'icon' => 'ti ti-clock-x',
        ],
        'false' => [
            'original' => 'false',
            'converted' => 'Còn hạn',
            'color' => 'success',
            'icon' => 'ti ti-clock-check',
        ],
    ];

    public function isQuantityExceeded($key)
    {
        return $this->getValueFromArrayByKey(self::IS_QUANTITY_EXCEEDED, $key);
    }

    public function isExpired($key)
    {
        return $this->getValueFromArrayByKey(self::IS_EXPIRED, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
