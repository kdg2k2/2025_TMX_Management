<?php

namespace App\Repositories;

use App\Models\ProofContract;

class ProofContractRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ProofContract();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => [
                    'name',
                ]
            ]
        ];
    }
}
