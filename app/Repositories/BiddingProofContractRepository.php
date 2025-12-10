<?php

namespace App\Repositories;

use App\Models\BiddingProofContract;

class BiddingProofContractRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingProofContract();
        $this->relations = [
            'createdBy',
            'proofContract',
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['bidding_id']))
            $query->where('bidding_id', $request['bidding_id']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'proofContract' => ['name'],
            ]
        ];
    }
}
