<?php
namespace App\Services;

use App\Repositories\JobTitleRepository;

class JobTitleService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(JobTitleRepository::class);
    }
}
