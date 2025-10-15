<?php
namespace App\Repositories;

use App\Models\ContractFile;

class ContractFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractFile();
        $this->relations = [
            'type',
            'createdBy',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['contract_id']))
            $query->where('contract_id', $request['contract_id']);
        if (isset($request['type_id']))
            $query->where('type_id', $request['type_id']);
    }

    public function list(array $request = [], ?callable $searchFunc = null)
    {
        $searchFunc = function ($query) use ($request) {
            if (empty($request['search']))
                return;

            $search = "%{$request['search']}%";

            $query->where(function ($q) use ($search) {
                $q
                    ->where('updated_content', 'like', $search)
                    ->orWhere('note', 'like', $search)
                    ->orWhereHas('createdBy', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    })
                    ->orWhereHas('type', function ($q) use ($search) {
                        $q->where('name', 'like', $search);
                    });
            });
        };

        return parent::list($request, $searchFunc);
    }
}
