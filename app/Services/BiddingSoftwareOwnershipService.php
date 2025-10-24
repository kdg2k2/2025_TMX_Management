<?php

namespace App\Services;

use App\Repositories\BiddingSoftwareOwnershipRepository;

class BiddingSoftwareOwnershipService extends BaseService
{
    public function __construct(
        private SoftwareOwnershipService $softwareOwnershipService
    ) {
        $this->repository = app(BiddingSoftwareOwnershipRepository::class);
    }

    public function updateOrCreate(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            return $this->repository->updateOrCreate($request);
        }, true);
    }

    public function deleteBySoftwareOwnershipId(int $id)
    {
        return $this->tryThrow(function () use ($id) {
            return $this->repository->findByKey($id, 'software_ownership_id', false)->delete();
        }, true);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['software_ownership']))
            $array['software_ownership'] = $this->softwareOwnershipService->formatRecord($array['software_ownership']);

        return $array;
    }
}
