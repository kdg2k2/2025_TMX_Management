<?php
namespace App\Services;

use App\Repositories\DepartmentRepository;

class DepartmentService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(DepartmentRepository::class);
    }
}
