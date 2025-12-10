<?php

namespace App\Services;

use App\Repositories\BiddingContractorExperienceRepository;

class BiddingContractorExperienceService extends BaseService
{
    public function __construct(
        private ContractService $contractService
    ) {
        $this->repository = app(BiddingContractorExperienceRepository::class);
    }

    public function updateOrCreate(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            return $this->repository->updateOrCreate([
                'contract_id' => $request['contract_id'],
                'bidding_id' => $request['bidding_id'],
            ], $request);
        }, true);
    }

    public function getFileType($key = null)
    {
        return $this->tryThrow(function () use ($key) {
            return $this->repository->getFileType($key);
        });
    }

    public function deleteByContractId(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            return $this->repository->findByKey($id, 'contract_id', false)->delete();
        }, true);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['file_type']))
            $array['file_type'] = $this->repository->getFileType($array['file_type']);

        if (isset($array['contract']))
            $array['contract'] = $this->contractService->formatRecord($array['contract']);

        return $array;
    }
}
