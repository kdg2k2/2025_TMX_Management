<?php

namespace App\Services;

use App\Repositories\PersonnelUnitRepository;

class PersonnelUnitService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(PersonnelUnitRepository::class);
    }
}
