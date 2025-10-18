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

    protected function applySearch($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q
                ->where('name', 'like', $search)
                ->orWhere('description', 'like', $search);
        });
    }
}
