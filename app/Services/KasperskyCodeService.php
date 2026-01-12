<?php

namespace App\Services;

use App\Repositories\KasperskyCodeRepository;
use Carbon\Carbon;

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
            $request['path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['path'], $this->repository->model->getTable());
        return $request;
    }

    protected function beforeUpdate(array $request)
    {
        if (isset($request['path']))
            $request['path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['path'], $this->repository->model->getTable(), null, $this->repository->findById($request['id'])['path']);
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
}
