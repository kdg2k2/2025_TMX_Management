<?php
namespace App\Services;

use App\Models\PersonnelFile;
use App\Repositories\PersonnelFileRepository;

class PersonnelFileService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private PersonnelFileTypeService $personnelFileTypeService,
        private StringHandlerService $stringHandlerService
    ) {
        $this->repository = app(PersonnelFileRepository::class);
    }

    public function baseDataForLCEView(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);
        $res['personnels'] = app(\App\Services\PersonnelService::class)->list();
        $res['personnelFileTypes'] = $this->personnelFileTypeService->list();
        return $res;
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

    public function getFolderOnGoogleDrive(PersonnelFile $data)
    {
        return "NhanSu/{$data['personnel']['personnelUnit']['short_name']}/{$this->stringHandlerService->createPascalSlug($data['personnel']['name'])}";
    }

    private function handleFile(PersonnelFile $data, array $extracted, bool $isUpdate = false)
    {
        $data->load($this->repository->relations);

        if ($extracted['path']) {
            $oldFile = $isUpdate ? $data['path'] : null;
            $data['path'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['path'], 'personnels', 'files', $oldFile);
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
