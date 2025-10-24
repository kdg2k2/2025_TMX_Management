<?php

namespace App\Repositories;

use App\Models\BiddingImplementationPersonnelFile;

class BiddingImplementationPersonnelFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingImplementationPersonnelFile();
        $this->relations = [
            'personelFile'
        ];
    }
}
