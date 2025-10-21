<?php

namespace App\Services;

use App\Repositories\BiddingRepository;

class BiddingService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(BiddingRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);

        return $res;
    }
}
