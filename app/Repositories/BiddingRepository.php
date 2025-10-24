<?php

namespace App\Repositories;

use App\Models\Bidding;

class BiddingRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Bidding();
        $this->relations = [
            'createdBy',
            'biddingImplementationPersonnel' => [
                'personnel',
                'files.personelFile',
            ],
            'biddingContractorExperience.contract',
            'biddingEligibility.eligibility',
            'biddingOrtherFile',
            'biddingProofContract.proofContract',
            'biddingSoftwareOwnership.softwareOwnership',
        ];
    }

    protected function getSearchConfig(): array
    {
        return [
            'text' => [
                'name',
            ],
            'date' => [],
            'datetime' => [],
            'relations' => [
                'createdBy' => [
                    'name',
                ]
            ]
        ];
    }
}
