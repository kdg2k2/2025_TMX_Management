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

    public function list(array $request = [], callable|null $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            if (empty($request['search']))
                return;

            $search = "%{$request['search']}%";
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        };

        return parent::list($request, $searchFunc);
    }
}
