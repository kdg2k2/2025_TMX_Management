<?php

namespace App\Services;

use App\Repositories\BiddingEligibilityRepository;

class BiddingEligibilityService extends BaseService
{
    public function __construct(
        private EligibilityService $eligibilityService
    ) {
        $this->repository = app(BiddingEligibilityRepository::class);
    }

    public function updateOrCreate(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            return $this->repository->updateOrCreate($request);
        }, true);
    }

    public function deleteByEligibilityIdRequest(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            return $this->repository->findByKey($id, 'eligibility_id', false)->delete();
        }, true);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['eligibility']))
            $array['eligibility'] = $this->eligibilityService->formatRecord($array['eligibility']);

        return $array;
    }
}
