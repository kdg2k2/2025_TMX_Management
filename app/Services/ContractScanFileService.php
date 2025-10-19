<?php

namespace App\Services;

use App\Models\ContractScanFile;
use App\Repositories\ContractScanFileRepository;

class ContractScanFileService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractScanFileRepository::class);
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

    private function handleFile(ContractScanFile $data, array $extracted, bool $isUpdate = false)
    {
        if ($extracted['path']) {
            $oldFile = $isUpdate ? $data['path'] : null;
            $data['path'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path'], 'contract', 'scan_files', $oldFile);
            $data->save();
        }
    }

    public function viewFile(int $id)
    {
        $data = $this->repository->findById($id, false);
        return $this->getAssetUrl($data['path']);
    }

    public function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles($entity['path'] ?? null);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);
        return $array;
    }
}
