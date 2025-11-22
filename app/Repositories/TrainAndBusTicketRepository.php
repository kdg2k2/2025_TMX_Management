<?php

namespace App\Repositories;

use App\Models\TrainAndBusTicket;

class TrainAndBusTicketRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TrainAndBusTicket();
        $this->relations = [
            'createdBy',
            'contract'
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'other_program_name',
                'expected_departure',
                'expected_destination',
            ],
            'date' => [
                'estimated_travel_time',
            ],
            'datetime' => [],
            'relations' => []
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }
}
