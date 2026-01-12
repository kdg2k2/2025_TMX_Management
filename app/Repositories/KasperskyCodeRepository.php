<?php

namespace App\Repositories;

use App\Models\KasperskyCode;

class KasperskyCodeRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new KasperskyCode();
        $this->relations = [
            'createdBy:id,name'
        ];
    }

    public function isQuantityExceeded($key = null)
    {
        return $this->model->isQuantityExceeded($key);
    }

    public function isExpired($key = null)
    {
        return $this->model->isExpired($key);
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'code',
                'total_quantity',
                'used_quantity',
                'valid_days',
            ],
            'date' => [
                'started_at',
                'expired_at',
            ],
            'datetime' => [],
            'relations' => [
                'createdBy' => ['name']
            ]
        ];
    }

    protected function applyListFilters($query, array $request)
    {
        foreach ([
            'is_quantity_exceeded',
            'is_expired',
        ] as $item)
            if (isset($request[$item]))
                $query->where($item, $request[$item]);
    }

    public function getExpiredCodes()
    {
        return $this
            ->model
            ->where('is_expired', false)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->get();
    }
}
