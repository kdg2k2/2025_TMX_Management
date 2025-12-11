<?php

namespace App\Repositories;

use App\Models\Airport;

class AirportRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Airport();
        $this->relations = [];
    }

    protected function getSearchConfig(): array
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
