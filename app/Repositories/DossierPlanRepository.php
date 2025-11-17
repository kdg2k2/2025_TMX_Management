<?php

namespace App\Repositories;

use App\Models\DossierPlan;

class DossierPlanRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new DossierPlan();
        $this->relations = [
            'minutes',
            'contract',
            'user',
            'details' => function ($q) {
                $q->with([
                    'type',
                    'province',
                    'commune',
                    'unit',
                    'responsible_user',
                ]);
            },
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['year']))
            $query->whereHas('contract', function ($q) use ($request) {
                $q->where('year', $request['year']);
            });

        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
    }

    public function findByIdContractAndYear(int $idContract, int $year = null)
    {
        $query = $this
            ->model
            ->with($this->relations);

        if ($year) {
            $query->whereHas('contract', function ($q) use ($year) {
                $q->where('year', $year);
            });
        }

        return $query->where('contract_id', $idContract)->first();
    }
}
