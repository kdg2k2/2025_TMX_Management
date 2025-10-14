<?php
namespace App\Repositories;

use App\Models\ContractFile;

class ContractFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractFile();
        $this->relations = [
            'type',
            'createdBy',
        ];
    }
}
