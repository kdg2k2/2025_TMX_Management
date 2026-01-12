<?php

namespace App\Repositories;

use App\Models\KasperskyCode;
use Illuminate\Support\Facades\DB;

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

    public function statistic(array $request = [])
    {
        $query = $this->model->query();

        if (isset($request['year']))
            $query->whereYear('created_at', $request['year']);
        if (isset($request['month']))
            $query->whereMonth('created_at', $request['month']);

        $result = $query->selectRaw('
            COUNT(*) as total_codes,
            SUM(total_quantity) as total_quantity,
            SUM(used_quantity) as used_quantity,
            SUM(available_quantity) as available_quantity,
            COUNT(CASE WHEN is_expired = 1 THEN 1 END) as expired_codes,
            COUNT(CASE WHEN is_quantity_exceeded = 1 THEN 1 END) as quantity_exceeded_codes
        ')->first();

        return [
            'total_codes' => (int) $result->total_codes,
            'total_quantity' => (int) ($result->total_quantity ?? 0),
            'used_quantity' => (int) ($result->used_quantity ?? 0),
            'available_quantity' => (int) ($result->available_quantity ?? 0),
            'expired_codes' => (int) $result->expired_codes,
            'quantity_exceeded_codes' => (int) $result->quantity_exceeded_codes,
        ];
    }

}
