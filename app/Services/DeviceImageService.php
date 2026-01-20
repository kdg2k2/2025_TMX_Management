<?php

namespace App\Services;

use App\Repositories\DeviceImageRepository;

class DeviceImageService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(DeviceImageRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetImage($array['path']);
        return $array;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $this->repository->insert(array_map(function ($item) use ($request) {
                return [
                    'device_id' => $request['device_id'],
                    'path' => $this->handleImage($item),
                    'created_at' => now(),
                ];
            }, $request['path']));
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $data->update([
                'path' => $this->handleImage($request['path'], $data['path']),
            ]);
            return $data;
        }, true);
    }

    private function handleImage($image, $oldImage = null)
    {
        return $this->handlerUploadFileService->storeAndRemoveOld($image, $this->repository->getTable(), null, $oldImage);
    }

    protected function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles($entity['path']);
    }
}
