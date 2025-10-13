<?php
namespace App\Repositories;

use App\Models\Contract;
use App\Repositories\BaseRepository;

class ContractRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Contract();
        $this->relations = [
            'createdBy',
            'instructors',
            'accountingContact',
            'inspectorUser',
            'executorUser',
            'type',
            'investor',
            'manyYears',
            'extensions',
            'scopes.province',
            'professionals',
            'disbursements',
            'intermediateCollaborators',
        ];
    }
}
