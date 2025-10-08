<?php

namespace App\Services;

use App\Repositories\ProvinceRepository;

class ProvinceService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ProvinceRepository::class);
    }

    public function findByCode(int $code)
    {
        return $this->tryThrow(function () use ($code) {
            return $this->repository->findByKey($code, 'code');
        });
    }
}
