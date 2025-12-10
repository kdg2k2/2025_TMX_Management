<?php

namespace App\Services;

use App\Repositories\BiddingProofContractRepository;

class BiddingProofContractService extends BaseService
{
    public function __construct(
        private ProofContractService $proofContractService
    ) {
        $this->repository = app(BiddingProofContractRepository::class);
    }

    public function updateOrCreate(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            return $this->repository->updateOrCreate([
                'proof_contract_id' => $request['proof_contract_id'],
                'bidding_id' => $request['bidding_id'],
            ], $request);
        }, true);
    }

    public function deleteByProofContractId(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            return $this->repository->findByKey($id, 'proof_contract_id', false)->delete();
        }, true);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['proof_contract']))
            $array['proof_contract'] = $this->proofContractService->formatRecord($array['proof_contract']);

        return $array;
    }
}
