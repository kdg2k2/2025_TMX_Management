<?php

namespace App\Repositories;

use App\Models\ContractUnit;

class ContractUnitRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractUnit();
        $this->relations = [];
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
                    ->orWhere('address', 'like', $search);
            });
        };

        return parent::list($request, $searchFunc);
    }
}
