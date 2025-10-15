<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait PaginateTraits
{
    public function paginateOrNot(array $request, array $data)
    {
        $request['paginate'] = $request['paginate'] ?? false;
        $request['per_page'] = $request['per_page'] ?? 10;
        $request['page'] = $request['page'] ?? 1;

        if ($request['paginate'])
            $data = $this->paginate($data, $request['per_page'], $request['page']);
        return $data;
    }

    public function paginate($data, $perPage, $page)
    {
        return new LengthAwarePaginator(
            collect($data)->forPage($page, $perPage)->values(),
            count($data),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
