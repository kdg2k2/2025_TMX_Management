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
            'instructors.user',
            'accountingContact',
            'inspectorUser',
            'executorUser',
            'type',
            'investor',
            'manyYears',
            'scopes.province',
            'professionals.user',
            'disbursements.user',
            'intermediateCollaborators.user',
            'appendixes' => fn($q) => $q->orderByDesc('times'),
            'finances',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'short_name',
                'year',
                'contract_number',
                'contract_value',
                'vat_rate',
                'vat_amount',
            ],
            'date' => [
                'signed_date',
                'end_date',
            ],
            'datetime' => [],
            'relations' => [
                'scopes' => ['name'],
                'type' => ['name'],
                'investor' => ['name'],
            ]
        ];
    }

    public function isJointVentureContract(int $id)
    {
        return $this->model->find($id)->type_id == 2;
    }
}
