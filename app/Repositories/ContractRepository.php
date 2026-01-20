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

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['intermediate_product_status']))
            $query->where('intermediate_product_status', $request['intermediate_product_status']);
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

    public function getYears()
    {
        return $this->model->pluck('year')->unique()->sortByDesc('year')->toArray();
    }

    public function getContractStatus($key = null)
    {
        return $this->model->getContractStatus($key);
    }

    public function getIntermediateProductStatus($key = null)
    {
        return $this->model->getIntermediateProductStatus($key);
    }

    public function getFinancialStatus($key = null)
    {
        return $this->model->getFinancialStatus($key);
    }
}
