<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bidding extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function biddingContractorExperience(){
        return $this->hasMany(BiddingContractorExperience::class);
    }

    public function biddingEligibility(){
        return $this->hasMany(BiddingEligibility::class);
    }

    public function biddingImplementationPersonnel(){
        return $this->hasMany(BiddingImplementationPersonnel::class);
    }

    public function biddingOrtherFile(){
        return $this->hasMany(BiddingOrtherFile::class);
    }

    public function biddingProofContract(){
        return $this->hasMany(BiddingProofContract::class, 'bidding_id');
    }

    public function biddingSoftwareOwnership(){
        return $this->hasMany(BiddingSoftwareOwnership::class, 'bidding_id');
    }
}
