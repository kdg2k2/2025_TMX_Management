<?php
namespace App\Services;

use App\Models\ContractFile;
use App\Repositories\ContractFileRepository;

class ContractFileService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private StringHandlerService $stringHandlerService
    ) {
        $this->repository = app(ContractFileRepository::class);
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

    public function getFolderOnGoogleDrive(ContractFile $data)
    {
        $contractService = app(ContractService::class);
        return $contractService->getFolderOnGoogleDrive($contractService->findById($data['contract_id'])) . '/Contracts/Files/' . $this->stringHandlerService->createPascalSlug($data['type']['name']);
    }

    private function handleFile(ContractFile $data, array $extracted, bool $isUpdate = false)
    {
        $data->load($this->repository->relations);

        if ($extracted['path'] && $data['type']['type'] != 'url') {
            $oldFile = $isUpdate ? $data['path'] : null;
            $data['path'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path'], 'contracts/files', $this->stringHandlerService->createPascalSlug($data['type']['name']), $oldFile);
            $data->save();

            \App\Jobs\UploadFileToDriveJob::dispatch(
                $this->handlerUploadFileService->getAbsolutePublicPath($data['path']),
                $this->getFolderOnGoogleDrive($data),
                null,
                false,
                false,
                $oldFile ? $this->getFolderOnGoogleDrive($data) . '/' . basename($oldFile) : null
            );
        }
    }

    public function afterDelete($entity)
    {
        if ($entity['path']) {
            $this->handlerUploadFileService->removeFiles($entity['path']);

            \App\Jobs\DeleteFileFromDriveJob::dispatch($this->getFolderOnGoogleDrive($entity) . '/' . basename($entity['path']));
        }
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['path']))
            $array['path'] = $this->getAssetUrl($array['path']);
        return $array;
    }
}
