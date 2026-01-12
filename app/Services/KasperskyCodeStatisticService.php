<?php

namespace App\Services;

class KasperskyCodeStatisticService extends BaseService
{
    public function __construct(
        private KasperskyCodeService $kasperskyCodeService,
        private KasperskyCodeRegistrationService $kasperskyCodeRegistrationService
    ) {}

    public function statistic(array $request)
    {
        return $this->tryThrow(fn() => [
            'counter' => $this->kasperskyCodeService->statistic($request),
            'excel' => $this->kasperskyCodeRegistrationService->statistic($request)
        ]);
    }
}
