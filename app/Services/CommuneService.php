<?php

namespace App\Services;

use App\Repositories\CommuneRepository;

class CommuneService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(CommuneRepository::class);
    }

    protected function beforeListQuery(array $request)
    {
        $request['order_by'] = 'code';
        $request['sort_by'] = 'desc';

        return $request;
    }

    public function findByCode(int $code)
    {
        return $this->tryThrow(function () use ($code) {
            return $this->repository->findByKey($code, 'code');
        });
    }
}
