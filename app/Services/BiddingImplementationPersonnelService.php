<?php

namespace App\Services;

use App\Repositories\BiddingImplementationPersonnelRepository;

class BiddingImplementationPersonnelService extends BaseService
{
    public function __construct(
        private PersonnelService $proofContractService,
        private BiddingImplementationPersonnelFileService $biddingImplementationPersonnelFileService
    ) {
        $this->repository = app(BiddingImplementationPersonnelRepository::class);
    }

    public function getJobTitle($key = null)
    {
        return $this->repository->getJobTitle($key);
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            if (isset($request['personnels'][0]['bidding_id']))
                $this->repository->deleteByBiddingId($request['personnels'][0]['bidding_id']);

            foreach ($request['personnels'] as $item) {
                $data = $this->repository->store([
                    'created_by' => $request['created_by'],
                    'bidding_id' => $item['bidding_id'],
                    'personnel_id' => $item['personnel_id'],
                ]);

                $this->syncRelationship($data, 'bidding_implementation_personnel_id', 'files', $item['files'], 'personnel_file_id');
            }
        }, true);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['implementation_personnel']))
            $array['implementation_personnel'] = $this->proofContractService->formatRecord($array['implementation_personnel']);

        if (isset($array['files']))
            $array['files'] = $this->biddingImplementationPersonnelFileService->formatRecords($array['files']);

        if (isset($array['job_title']))
            $array['job_title'] = $this->repository->getJobtitle($array['job_title']);

        return $array;
    }
}
