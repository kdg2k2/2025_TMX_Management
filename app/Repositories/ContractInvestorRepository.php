<?php
namespace App\Repositories;

use App\Models\ContractInvestor;

class ContractInvestorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractInvestor();
    }

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('name_vi', 'like', $search)
                ->orWhere('name_en', 'like', $search)
                ->orWhere('address', 'like', $search);
        });
    }
}
