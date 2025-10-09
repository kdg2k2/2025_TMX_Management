<?php
namespace App\Repositories;

use App\Models\ContractType;
use App\Repositories\BaseRepository;

class ContractTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractType();
    }
}
