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
            $query
                ->where('name', 'like', "%{$request['search']}%")
                ->orWhere('description', 'like', "%{$request['search']}%");
        };

        return parent::list($request, $searchFunc);
    }
}
