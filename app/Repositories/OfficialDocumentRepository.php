<?php

namespace App\Repositories;

use App\Models\OfficialDocument;

class OfficialDocumentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new OfficialDocument();
        $this->relations = [
            'createdBy:id,name',
            'approvedBy:id,name',
            'officialDocumentType:id,name',
            'officialDocumentSector:id,name',
            'users:id,name',
            'reviewedBy:id,name',
            'signedBy:id,name',
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

    public function getReleaseType($key = null)
    {
        return $this->model->getReleaseType($key);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'other_program_name',
                'name',
                'receiver_organization',
                'receiver_address',
                'receiver_name',
                'receiver_phone',
                'note',
                'document_number',
                'approval_note',
                'rejection_note',
            ],
            'date' => [
                'expected_release_date',
                'released_date',
            ],
            'datetime' => [
                'approved_at',
            ],
            'relations' => [
                'createdBy' => ['name'],
                'approvedBy' => ['name'],
                'officialDocumentType' => ['name'],
                'officialDocumentSector' => ['name'],
                'users' => ['name'],
                'reviewedBy' => ['name'],
                'signedBy' => ['name'],
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'release_type',
            'program_type',
            'status',
        ] as $item) {
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
        }
    }
}
