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

    private function formatShortName(string $string)
    {
        return app(StringHandlerService::class)->createUpperSnakeCaseSlug($string);
    }

    public function beforeStore(array $request)
    {
        $request['short_name'] = $this->formatShortName($request['short_name']);
        return $request;
    }

    public function beforeUpdate(array $request)
    {
        $request['short_name'] = $this->formatShortName($request['short_name']);
        return $request;
    }
}
