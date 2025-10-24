<?php

namespace App\Services;

use App\Repositories\BiddingImplementationPersonnelFileRepository;

class BiddingImplementationPersonnelFileService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(BiddingImplementationPersonnelFileRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['personel_file']))
            $array['personel_file'] = app(PersonnelFileService::class)->formatRecord($array['personel_file']);
        return $array;
    }
}
