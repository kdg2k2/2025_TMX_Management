<?php

namespace App\Repositories;

use App\Models\BiddingEligibility;

class BiddingEligibilityRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingEligibility();
        $this->relations = [
            'createdBy',
            'eligibility',
        ];
    }

    public function updateOrCreate(array $request)
    {
        return $this->model->updateOrCreate([
            'eligibility_id' => $request['eligibility_id'],
            'bidding_id' => $request['bidding_id'],
        ], $request);
    }
}
