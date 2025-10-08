<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function accountingContact()
    {
        return $this->belongsTo(User::class, 'accounting_contact_id');
    }

    public function inspectorUser()
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }

    public function executorUser()
    {
        return $this->belongsTo(User::class, 'executor_user_id');
    }

    public function type()
    {
        return $this->belongsTo(ContractType::class, 'type_id');
    }

    public function investor()
    {
        return $this->belongsTo(ContractInvestor::class, 'investor_id');
    }
}
