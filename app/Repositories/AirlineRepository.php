<?php

namespace App\Repositories;

use App\Models\Airline;

class AirlineRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Airline();
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
