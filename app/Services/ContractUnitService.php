<?php
namespace App\Services;

use App\Models\ContractUnit;
use App\Repositories\ContractUnitRepository;

class ContractUnitService extends BaseService
{
    public function __construct(
        private FileExtensionService $fileExtensionService
    ) {
        $this->repository = app(ContractUnitRepository::class);
    }

    public function getCreateOrUpdateData(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->findById($id, true, true);

        return $res;
    }
}
