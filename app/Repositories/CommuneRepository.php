<?php

namespace App\Repositories;

use App\Models\Commune;

class CommuneRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Commune();
    }

    public function findByCode(int $code)
    {
        return $this->model->where('code', $code)->first();
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $request['order_by'] = 'code';
        $request['sort_by'] = 'desc';

        $searchFunc = function ($query) use ($request) {
            if (empty($request['search']))
                return;

            $search = "%{$request['search']}%";
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', $search)
                    ->orWhere('code', 'like', $search);
            });
        };

        return parent::list($request, $searchFunc);
    }
}
