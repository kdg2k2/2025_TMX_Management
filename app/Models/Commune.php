<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $primaryKey = 'code';  // sử dụng 'code' làm khóa chính
    public $incrementing = false;  // vì 'code' không tự tăng
    protected $keyType = 'string';

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}
