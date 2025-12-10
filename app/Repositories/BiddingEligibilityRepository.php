<?php

namespace App\Repositories;

use App\Models\BiddingEligibility;

class BiddingEligibilityRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingEligibility();
        $this->relations = [
            'createdBy',
            'eligibility',
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
                'eligibility' => ['name'],
            ]
        ];
    }
}
