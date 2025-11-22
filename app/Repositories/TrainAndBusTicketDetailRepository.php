<?php

namespace App\Repositories;

use App\Models\TrainAndBusTicketDetail;

class TrainAndBusTicketDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new TrainAndBusTicketDetail();
        $this->relations = [
            'createdBy',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'departure_place',
                'return_place',
                'train_number',
                'ticket_price',
                'note',
            ],
            'date' => [
                'departure_date',
                'return_date',
            ],
            'datetime' => [],
            'relations' => []
        ];
    }

    public function getUserType($key = null)
    {
        return $this->model->getUserType($key);
    }
}
