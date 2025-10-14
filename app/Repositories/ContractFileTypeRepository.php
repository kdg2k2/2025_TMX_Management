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
}
