<?php
namespace App\Repositories;

use App\Models\JobTitle;
use App\Repositories\BaseRepository;

class JobTitleRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new JobTitle();
    }
}
