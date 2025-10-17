<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAdvancePayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function finance(){
        return $this->belongsTo(ContractFinance::class);
    }
}
