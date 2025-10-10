<?php
namespace App\Repositories;

use App\Models\ContractInvestor;

class ContractInvestorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractInvestor();
    }

    public function list(array $request = [], callable|null $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            $query
                ->where('name', 'like', "%{$request['search']}%")
                ->orWhere('address', 'like', "%{$request['search']}%");
        };

        return parent::list($request, $searchFunc);
    }
}
