<?php

namespace App\Services;

use App\Repositories\KasperskyCodeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KasperskyCodeService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(KasperskyCodeRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);
        if (isset($array['is_quantity_exceeded']))
            $array['is_quantity_exceeded'] = $this->repository->isQuantityExceeded($array['is_quantity_exceeded']);
        if (isset($array['is_expired']))
            $array['is_expired'] = $this->repository->isExpired($array['is_expired']);
        if (isset($array['expired_at'])) {
            $today = Carbon::today();
            $expiredAt = Carbon::parse($array['expired_at']);

            $array['remaining_days'] = $expiredAt->isPast()
                ? 0
                : $today->diffInDays($expiredAt);
        }
        foreach ([
            'started_at',
            'expired_at',
        ] as $item)
            if (isset($array[$item]))
                $array[$item] = $this->formatDateForPreview($array[$item]);
        if (isset($array['available_quantity'])) {
            $array['available_quantity_message'] = "{$array['available_quantity']} lượt";
        }
        return $array;
    }

    protected function beforeStore(array $request)
    {
        if (isset($request['path']))
            $request['path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['path'], $this->repository->getTable());
        return $request;
    }

    protected function beforeUpdate(array $request)
    {
        if (isset($request['path']))
            $request['path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['path'], $this->repository->getTable(), null, $this->repository->findById($request['id'])['path']);
        if (isset($request['valid_days']) && isset($request['started_at']))
            $request['expired_at'] = Carbon::parse($request['started_at'])
                ->addDays((int) $request['valid_days'])
                ->toDateString();
        return $request;
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles($entity['path']);
    }

    public function checkExpiredKasperskyCodes()
    {
        return $this->tryThrow(function () {
            $expiredCodes = $this->repository->getExpiredCodes();
            if ($expiredCodes->isEmpty())
                return;

            $count = 0;
            $expiredCodesList = [];

            foreach ($expiredCodes as $code) {
                $code->update(['is_expired' => true]);
                $count++;
                $expiredCodesList[] = [
                    'code' => $code['code'],
                    'expired_at' => $this->formatDateForPreview($code['expired_at']),
                ];
            }

            Log::info('Kaspersky codes expired check completed', [
                'total_expired' => $count,
                'codes' => $expiredCodesList,
                'checked_at' => now()->format('Y-m-d H:i:s'),
            ]);
        }, true);
    }

    public function statistic(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $config = [
                'total_codes' => [
                    'converted' => 'Tổng số mã',
                    'color' => 'primary',
                    'icon' => 'ti ti-database',
                ],
                'total_quantity' => [
                    'converted' => 'Tổng lượt cho phép',
                    'color' => 'info',
                    'icon' => 'ti ti-packages',
                ],
                'used_quantity' => [
                    'converted' => 'Lượt đã sử dụng',
                    'color' => 'success',
                    'icon' => 'ti ti-check',
                ],
                'available_quantity' => [
                    'converted' => 'Lượt còn lại',
                    'color' => 'warning',
                    'icon' => 'ti ti-hourglass-high',
                ],
                'expired_codes' => [
                    'converted' => 'Mã hết hạn',
                    'color' => 'danger',
                    'icon' => 'ti ti-calendar-x',
                ],
                'quantity_exceeded_codes' => [
                    'converted' => 'Mã hết lượt',
                    'color' => 'danger',
                    'icon' => 'ti ti-ban',
                ],
            ];

            $statistic = $this->repository->statistic($request);

            return collect($config)->map(function ($item, $key) use ($statistic) {
                return [
                    'original' => $key,
                    'converted' => $item['converted'],
                    'color' => $item['color'],
                    'icon' => $item['icon'],
                    'value' => $statistic[$key] ?? 0,
                ];
            })->values()->toArray();
        });
    }
}
