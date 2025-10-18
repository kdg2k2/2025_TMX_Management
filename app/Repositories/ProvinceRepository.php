<?php

namespace App\Repositories;

use App\Models\Province;

class ProvinceRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Province();
        $this->relations = [
            'communes',
        ];
    }

    public function findByCode(int $code)
    {
        return $this->model->where('code', $code)->with($this->relations)->first();
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
