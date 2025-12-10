<?php

namespace App\Repositories;

use App\Models\PlaneTicket;

class PlaneTicketRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PlaneTicket();
        $this->relations = [
            'contract:id,name',
            'airport:id,name',
            'airline:id,name',
            'planeTicketClass:id,name',
            'createdBy:id,name,path',
            'approvedBy:id,name,path',
            'details.user:id,name,path',
        ];
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }

    public function getStatus($key)
    {
        return $this->model->getStatus($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [
                'other_program_name',
                'checked_baggage_allowances',
                'approval_note',
                'rejection_note',
            ],
            'date' => [],
            'datetime' => [
                'estimated_flight_time'
            ],
            'relations' => [
                'contract' => ['name'],
                'airport' => ['name'],
                'airline' => ['name'],
                'planeTicketClass' => ['name'],
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
                'details' => ['name'],
            ]
        ];
    }
}
