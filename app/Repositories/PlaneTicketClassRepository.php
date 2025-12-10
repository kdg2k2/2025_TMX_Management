<?php

namespace App\Repositories;

use App\Models\PlaneTicketClass;

class PlaneTicketClassRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new PlaneTicketClass();
        $this->relations = [];
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
