<?php
namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(RoleRepository::class);
    }
}
