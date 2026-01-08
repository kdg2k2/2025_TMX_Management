<?php

namespace App\Services;

use App\Repositories\IncomingOfficialDocumentUserRepository;

class IncomingOfficialDocumentUserService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(IncomingOfficialDocumentUserRepository::class);
    }
}
