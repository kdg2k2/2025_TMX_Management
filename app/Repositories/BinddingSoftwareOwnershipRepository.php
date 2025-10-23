<?php

namespace App\Repositories;

use App\Models\BinddingSoftwareOwnership;

class BinddingSoftwareOwnershipRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BinddingSoftwareOwnership();
        $this->relations = [
            'createdBy',
            'softwareOwnership',
        ];
    }

    public function updateOrCreate(array $request)
    {
        return $this->model->updateOrCreate([
            'software_ownership_id' => $request['software_ownership_id'],
            'bidding_id' => $request['bidding_id'],
        ], $request);
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
