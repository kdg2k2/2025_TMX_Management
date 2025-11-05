<?php

namespace App\Services;

use App\Models\BoardMeetingMinute;
use App\Repositories\BoardMeetingMinuteRepository;

class BoardMeetingMinuteService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
    ) {
        $this->repository = app(BoardMeetingMinuteRepository::class);
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->store($request);
            $this->handleFile($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleFile($data, $extracted, true);
        }, true);
    }

    private function extractFields(array &$request): array
    {
        $fields = [
            'path',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleFile(BoardMeetingMinute $data, array $extracted, bool $isUpdate = false)
    {
        if ($extracted['path']) {
            $oldFile = $isUpdate ? $data['path'] : null;
            $data['path'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path'], $this->repository->model->getTable(),
                'files', $oldFile);
            $data->save();
        }
    }

    public function afterDelete($entity)
    {
        if ($entity['path'])
            $this->handlerUploadFileService->removeFiles($entity['path']);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['meeting_day']))
            $array['meeting_day'] = $this->formatDateForPreview($array['meeting_day']);
        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);
        return $array;
    }
}
