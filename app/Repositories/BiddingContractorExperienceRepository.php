<?php

namespace App\Repositories;

use App\Models\BiddingContractorExperience;

class BiddingContractorExperienceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingContractorExperience();
        $this->relations = [
            'createdBy',
            'contract',
        ];
    }

    public function getFileType($key = null)
    {
        return $this->model->getFileType($key);
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
                'contract' => ['name'],
            ]
        ];
    }
}
