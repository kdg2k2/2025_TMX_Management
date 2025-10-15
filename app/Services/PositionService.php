<?php
namespace App\Services;

use App\Repositories\PositionRepository;

class PositionService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(PositionRepository::class);
    }
}
