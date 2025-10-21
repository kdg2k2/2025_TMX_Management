<?php

namespace App\Services;

use App\Repositories\BiddingRepository;

class BiddingService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(BiddingRepository::class);
    }
}
