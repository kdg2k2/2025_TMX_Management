<?php

namespace App\Repositories;

use App\Models\ContractProductMinuteSignature;

class ContractProductMinuteSignatureRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new ContractProductMinuteSignature();
        $this->relations = [];
    }

    public function getStatus($key = null)
    {
        return $this->model->getStatus($key);
    }

    public function getType($key = null)
    {
        return $this->model->getType($key);
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'type',
            'status',
            'contract_product_minute_id',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [],
            'date' => [],
            'datetime' => [
                'signed_at'
            ],
            'relations' => [
                'user' => ['name']
            ]
        ];
    }

    public function getMinuteSignByUserId(int $userId, int $minuteId)
    {
        return $this->model->where('user_id', $userId)->where('contract_product_minute_id', $minuteId)->first();
    }

    public function isMinuteSigned(int $minuteId)
    {
        return !$this->model->where('contract_product_minute_id', $minuteId)->where('status', '!=', 'signed')->exists();
    }
}
