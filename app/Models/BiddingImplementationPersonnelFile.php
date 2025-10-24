<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiddingImplementationPersonnelFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function biddingImplementationPersonnel()
    {
        return $this->belongsTo(BiddingImplementationPersonnel::class, 'bidding_implementation_personnel_id');
    }

    public function personelFile()
    {
        return $this->belongsTo(PersonnelFile::class, 'personnel_file_id');
    }
}
