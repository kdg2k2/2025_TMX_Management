<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $primaryKey = 'code';  // sử dụng 'code' làm khóa chính
    public $incrementing = false;  // vì 'code' không tự tăng
    protected $keyType = 'string';

    public function communes()
    {
        return $this->hasMany(Commune::class, 'province_code', 'code');
    }
}
