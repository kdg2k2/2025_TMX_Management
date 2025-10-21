<?php

namespace App\Services;

use App\Repositories\PersonnelRepository;

class PersonnelService extends BaseService
{
    public function __construct(
        private PersonnelUnitService $personnelUnitService
    ) {
        $this->repository = app(PersonnelRepository::class);
    }

    public function getCreateOrUpdateBaseData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        $res['personnelUnits'] = $this->personnelUnitService->list();

        return $res;
    }
}
