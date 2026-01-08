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
            'assingedBy:id,name',
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
            'text' => [
                'other_program_name',
                'document_number',
                'content_summary',
                'sender_address',
                'signer_name',
                'signer_position',
                'contact_person_name',
                'contact_person_address',
                'contact_person_phone',
                'notes',
                'attachment_file',
                'task_notes',
            ],
            'date' => [
                'issuing_date',
                'received_date',
                'task_completion_deadline',
            ],
            'datetime' => [
                'assign_at',
                'complete_at',
            ],
            'relations' => [
                'incomingOfficialDocumentUsers.user' => ['name'],
                'createdBy' => ['name'],
                'contract' => ['name', 'contract_number'],
                'officialDocumentType' => ['name'],
                'taskAssignee' => ['name'],
                'assingedBy' => ['name'],
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'official_document_type_id',
            'program_type',
            'status',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }
}
