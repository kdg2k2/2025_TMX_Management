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
            $query
                ->where('name', 'like', "%{$request['search']}%")
                ->orWhere('code', 'like', "%{$request['search']}%");
        };

        return parent::list($request, $searchFunc);
    }
}
