<?php

namespace App\Services;

use App\Repositories\UnitRepository;

class UnitService extends BaseService
{
    protected $provinceService;

    public function __construct()
    {
        $this->repository = app(UnitRepository::class);
        $this->provinceService = app(ProvinceService::class);
    }

    public function getNeededData(int $id = null)
    {
        return $this->tryThrow(function () use ($id) {
            $res = [];

            if (isset($id))
                $res['data'] = $this->repository->findById($id);
            $res['provinces'] = $this->provinceService->list([]);

            return $res;
        });
    }
}
