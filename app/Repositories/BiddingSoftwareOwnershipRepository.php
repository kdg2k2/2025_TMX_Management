<?php

namespace App\Repositories;

use App\Models\BiddingSoftwareOwnership;

class BiddingSoftwareOwnershipRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingSoftwareOwnership();
        $this->relations = [
            'createdBy',
            'softwareOwnership',
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
                'softwareOwnership' => ['name'],
            ]
        ];
    }
}
