<?php

namespace App\Services;

use App\Repositories\OfficialDocumentSectorEmailRepository;

class OfficialDocumentSectorEmailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(OfficialDocumentSectorEmailRepository::class);
    }
}
