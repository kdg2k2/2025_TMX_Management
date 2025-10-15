<?php
namespace App\Repositories;

use App\Models\ContractBill;

class ContractBillRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractBill();
        $this->relations = [
            'createdBy',
            'billCollector',
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
                    ->where('amount', 'like', $search)
                    ->orWhere('duration', 'like', $search)
                    ->orWhere('content_in_the_estimate', 'like', $search)
                    ->orWhere('note', 'like', $search)
                    ->orWhereHas('createdBy', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('billCollector', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    });
            });
        };

        return parent::list($request, $searchFunc);
    }
}
