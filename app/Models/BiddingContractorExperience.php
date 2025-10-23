<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingContractorExperience extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const FILE_TYPES = [
        'path_file_full' => [
            'original' => 'path_file_full',
            'converted' => 'FULL',
        ],
        'path_file_short' => [
            'original' => 'path_file_short',
            'converted' => 'SHORT',
        ],
    ];

    public function getFileType($key = null)
    {
        return $this->getValueFromArrayByKey(self::FILE_TYPES, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
