<?php

namespace App\Services;

use App\Repositories\VehicleRepository;

class VehicleService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(VehicleRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['inspection_expired_at']))
            $array['inspection_expired_at'] = $this->formatDateForPreview($array['inspection_expired_at']);
        if (isset($array['liability_insurance_expired_at']))
            $array['liability_insurance_expired_at'] = $this->formatDateForPreview($array['liability_insurance_expired_at']);
        if (isset($array['body_insurance_expired_at']))
            $array['body_insurance_expired_at'] = $this->formatDateForPreview($array['body_insurance_expired_at']);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        return $array;
    }

    public function baseDataForLCEView(int $id = null, bool $fullStatus = false)
    {
        $data = $id ? $this->repository->findById($id) : null;

        $current = $data['status'] ?? null;

        $status = collect($this->repository->getStatus())
            ->when(!$fullStatus, fn($q) =>
                $q->filter(fn($i) =>
                    $current === 'loaned'
                        ? $i['original'] === 'loaned'
                        : $i['original'] !== 'loaned'));

        return [
            'data' => $data,
            'status' => $status,
        ];
    }
}
