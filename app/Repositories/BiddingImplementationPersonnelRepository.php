<?php

namespace App\Repositories;

use App\Models\BiddingImplementationPersonnel;

class BiddingImplementationPersonnelRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingImplementationPersonnel();
        $this->relations = [
            'createdBy',
            'personnel',
        ];
    }

    public function getJobTitle($key = null)
    {
        return $this->model->getJobTitle($key);
    }

    public function deleteByBiddingId(int $idBidding)
    {
        return $this->model->where('bidding_id', $idBidding)->delete();
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['bidding_id']))
            $query->where('bidding_id', $request['bidding_id']);
    }
}
