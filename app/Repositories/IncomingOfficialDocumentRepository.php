<?php

namespace App\Repositories;

use App\Models\IncomingOfficialDocument;

class IncomingOfficialDocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new IncomingOfficialDocument();
        $this->relations = [
            'incomingOfficialDocumentUsers.user:id,name',
            'createdBy:id,name',
            'contract:id,name,contract_number',
            'officialDocumentType:id,name',
            'taskAssignee:id,name',
        ];
    }

    public function getProgramType($key = null)
    {
        return $this->model->getProgramType($key);
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    public function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'incomingOfficialDocumentUsers.user' => ['name'],
                'createdBy' => ['name'],
                'contract' => ['name', 'contract_number'],
                'officialDocumentType' => ['name'],
                'taskAssignee' => ['name'],
            ]
        ];
    }
}
