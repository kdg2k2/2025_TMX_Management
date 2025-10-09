<?php
namespace App\Services;

use App\Repositories\ContractTypeRepository;

class ContractTypeService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractTypeRepository::class);
    }
}
