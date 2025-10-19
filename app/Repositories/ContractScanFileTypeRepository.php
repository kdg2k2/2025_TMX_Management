<?php

namespace App\Repositories;

use App\Models\ContractScanFileType;

class ContractScanFileTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractScanFileType();
        $this->relations = [
            'extensions.extension',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
                'description',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => []
        ];
    }
}
