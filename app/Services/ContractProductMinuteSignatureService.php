<?php

namespace App\Services;

use App\Repositories\ContractProductMinuteSignatureRepository;

class ContractProductMinuteSignatureService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(ContractProductMinuteSignatureRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['type']))
            $array['type'] = $this->repository->getType($array['type']);
        if (isset($array['signature_path']))
            $array['signature_path'] = $this->getAssetUrl($array['signature_path']);
        if (isset($array['signed_at']))
            $array['signed_at'] = $this->formatDateTimeForPreview($array['signed_at']);
        return $array;
    }
}
