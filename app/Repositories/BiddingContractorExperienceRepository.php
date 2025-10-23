<?php

namespace App\Repositories;

use App\Models\BiddingContractorExperience;

class BiddingContractorExperienceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingContractorExperience();
        $this->relations = [
            'createdBy',
            'contract',
        ];
    }

    public function getFileType($key = null)
    {
        return $this->model->getFileType($key);
    }

    public function updateOrCreate(array $request)
    {
        return $this->model->updateOrCreate([
            'contract_id' => $request['contract_id'],
            'bidding_id' => $request['bidding_id'],
        ], $request);
    }
}
