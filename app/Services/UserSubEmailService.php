<?php

namespace App\Services;

use App\Repositories\UserSubEmailRepository;

class UserSubEmailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(UserSubEmailRepository::class);
    }
}
