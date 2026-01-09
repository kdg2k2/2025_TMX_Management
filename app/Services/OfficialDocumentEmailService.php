<?php

namespace App\Services;

use App\Repositories\OfficialDocumentEmailRepository;

class OfficialDocumentEmailService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(OfficialDocumentEmailRepository::class);
    }
}
