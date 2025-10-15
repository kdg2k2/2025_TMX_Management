<?php
namespace App\Repositories;

use App\Models\Department;
use App\Repositories\BaseRepository;

class DepartmentRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Department();
    }
}
