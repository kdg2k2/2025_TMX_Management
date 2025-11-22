<?php

namespace App\Services;

use App\Repositories\TrainAndBusTicketDetailRepository;

class TrainAndBusTicketDetailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(TrainAndBusTicketDetailRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['user_type'] = $this->repository->getUserType($array['user_type']);

        return $array;
    }
}
