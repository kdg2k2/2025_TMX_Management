<?php

namespace App\Services;

use App\Repositories\DeviceRepository;

class DeviceService extends BaseService
{
    public function __construct(
        private DeviceImageService $deviceImageService,
        private DeviceTypeService $deviceTypeService,
        private StringHandlerService $stringHandlerService
    ) {
        $this->repository = app(DeviceRepository::class);
    }

    public function getStatus($key = null)
    {
        return $this->repository->getStatus($key);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['images']))
            $array['images'] = $this->deviceImageService->formatRecords($array['images']);
        if (isset($array['current_status']))
            $array['current_status'] = $this->repository->getStatus($array['current_status']);
        return $array;
    }

    protected function beforeDelete(int $id)
    {
        $entity = parent::beforeDelete($id);
        foreach ($entity->images as $image)
            $this->deviceImageService->delete($image['id']);

        return $entity;
    }

    public function getBaseDataForLCEView(int $id = null)
    {
        $baseInfo = [
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
            ],
        ];

        $data = $id ? $this->repository->findById($id) : null;
        $status = $this->repository->getStatus();

        if ($current = $data['current_status'] ?? null) {
            $restricted = ['loaned', 'under_repair'];
            $status = collect($status)->filter(fn($i) =>
                in_array($current, $restricted)
                    ? $i['original'] == $current
                    : !in_array($i['original'], $restricted));
        }

        return [
            'data' => $data,
            'status' => $status,
            'deviceTypes' => $this->deviceTypeService->list($baseInfo),
        ];
    }

    protected function beforeStore(array $request)
    {
        $request['code'] = $this->stringHandlerService->generateRandomString();
        return $request;
    }

    public function statistic()
    {
        return $this->repository->statistic();
    }

    public function statisticByType()
    {
        return $this->repository->statisticByType();
    }

    public function statisticStatusByType()
    {
        return $this->repository->statisticStatusByType();
    }
}
