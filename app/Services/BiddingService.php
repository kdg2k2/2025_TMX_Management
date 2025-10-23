<?php

namespace App\Services;

use App\Repositories\BiddingRepository;

class BiddingService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(BiddingRepository::class);
    }

    public function getShowBaseData(int $id){
        return [
            'data' => $this->repository->findById($id, false),
            'biddingContractorExperienceFileTypes' => app(BiddingContractorExperienceService::class)->getFileType(),
        ];
    }
}
