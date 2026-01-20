<?php
namespace App\Repositories;

use App\Models\ContractFileType;

class ContractFileTypeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractFileType();
        $this->relations = [
            'extensions.extension',
        ];
    }

    public function getTypes($key = null)
    {
        return $this->model->getTypes($key);
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
