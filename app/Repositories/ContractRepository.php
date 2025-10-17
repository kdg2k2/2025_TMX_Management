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
            'appendixes' => function ($q) {
                $q->orderByDesc('times');
            },
            'finances',
        ];
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            if (empty($request['search']))
                return;

            $search = "%{$request['search']}%";

            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', $search)
                    ->orWhere('short_name', 'like', $search)
                    ->orWhere('year', 'like', $search)
                    ->orWhere('contract_number', 'like', $search)
                    ->orWhere('contract_value', 'like', $search)
                    ->orWhere('vat_rate', 'like', $search)
                    ->orWhere('vat_amount', 'like', $search)
                    ->orWhereHas('scopes', function ($scopeQuery) use ($search) {
                        $scopeQuery->whereHas('province', function ($provinceQuery) use ($search) {
                            $provinceQuery->where('name', 'like', $search);
                        });
                    })
                    ->orWhereHas('type', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('investor', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereRaw("DATE_FORMAT(signed_date, '%d/%m/%Y') LIKE ?", [$search])
                    ->orWhereRaw("DATE_FORMAT(end_date, '%d/%m/%Y') LIKE ?", [$search]);
            });
        };

        return parent::list($request, $searchFunc);
    }

    public function isJointVentureContract(int $id)
    {
        return $this->model->find($id)->type_id == 2;
    }
}
