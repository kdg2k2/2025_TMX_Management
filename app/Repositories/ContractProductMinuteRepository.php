<?php

namespace App\Repositories;

use App\Models\ContractProductMinute;

class ContractProductMinuteRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductMinute();
        $this->relations = [
            'signatures.user:id,name',
            'createdBy:id,name',
            'approvedBy:id,name',
            'contractProfessional.user:id,name',
            'contractDisbursement.user:id,name',
            'contract' => fn($q) => $q->select([
                'id',
                'year',
                'name',
                'contract_number',
            ])
        ];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'status',
            'contract_id',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'issue_note',
                'approval_note',
                'rejection_note',
            ],
            'date' => [],
            'datetime' => [
                'approved_at'
            ],
            'relations' => array_map(
                fn($i) => [
                    $i => ['name']
                ],
                [
                    'signatures.user',
                    'createdBy',
                    'approvedBy',
                ]
            )
        ];
    }

    public function getUsedPaths()
    {
        return $this->model->select('file_docx_path', 'file_pdf_path')->get();
    }
}
