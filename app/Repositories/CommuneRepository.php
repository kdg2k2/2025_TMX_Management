<?php

namespace App\Repositories;

use App\Models\Commune;

class CommuneRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Commune();
        $this->relations = [
            'province'
        ];
    }

    public function findByCode(int $code)
    {
        return $this->model->where('code', $code)->first();
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'code',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
