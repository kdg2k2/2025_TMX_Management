<?php

namespace App\Services;

use App\Models\InternalBulletin;
use App\Repositories\InternalBulletinRepository;

class InternalBulletinService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
    ) {
        $this->repository = app(InternalBulletinRepository::class);
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

    private function handleFile(InternalBulletin $data, array $extracted, bool $isUpdate = false)
    {
        if ($extracted['path']) {
            $oldFile = $isUpdate ? $data['path'] : null;
            $data['path'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path'], $this->repository->model->getTable(),
                'files', $oldFile);
            $data->save();
        }

        $date = date('d/m/Y', strtotime($data['created_at']));
        $subject = !$isUpdate ? "Bản tin mới $date" : "Cập nhật bản tin $date";
        $this->sendMail($data, $subject);
    }

    public function afterDelete($entity)
    {
        if ($entity['path'])
            $this->handlerUploadFileService->removeFiles($entity['path']);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);
        return $array;
    }

    private function sendMail(InternalBulletin $data, string $subject)
    {
        $files = isset($data['path']) ? [$this->handlerUploadFileService->getAbsolutePublicPath($data['path'])] : [];
        dispatch(new \App\Jobs\SendMailJob('emails.internal-bulletin', $subject, app(UserService::class)->getAllEmails(), [
            'data' => $this->formatRecord($data->toArray()),
        ], $files));
    }
}
