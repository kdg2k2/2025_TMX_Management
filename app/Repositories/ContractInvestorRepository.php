<?php
namespace App\Repositories;

use App\Models\ContractInvestor;

class ContractInvestorRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractInvestor();
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name_vi',
                'name_en',
                'address',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
