<?php

namespace App\Repositories;

use App\Models\PlaneTicketDetail;

class PlaneTicketDetailRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PlaneTicketDetail();
        $this->relations = [
            'createdBy:id,name,path',
            'user:id,name,path',
            'departureAirport:id,name',
            'returnAirport:id,name',
            'airline:id,name',
            'planeTicketClass:id,name',
        ];
    }

    public function getUserType($key = null)
    {
        return $this->model->getUserType($key);
    }

    protected function applyListFilters($query, array $request)
    {
        if (isset($request['plane_ticket_id']))
            $query->where('plane_ticket_id', $request['plane_ticket_id']);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'departure_place',
                'return_place',
                'train_number',
                'checked_baggage_allowances',
                'ticket_price',
                'note',
            ],
            'date' => [
                'departure_date',
                'return_date',
            ],
            'datetime' => [],
            'relations' => [
                'departureAirport' => ['name'],
                'returnAirport' => ['name'],
                'airline' => ['name'],
                'planeTicketClass' => ['name'],
            ]
        ];
    }
}
