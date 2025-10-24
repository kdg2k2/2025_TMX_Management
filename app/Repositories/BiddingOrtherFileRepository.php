<?php

namespace App\Repositories;

use App\Models\BiddingOrtherFile;

class BiddingOrtherFileRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BiddingOrtherFile();
        $this->relations = [
            'createdBy',
        ];
    }
}
