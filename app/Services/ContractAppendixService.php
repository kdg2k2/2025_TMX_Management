<?php
namespace App\Services;

use App\Models\ContractAppendix;
use App\Repositories\ContractAppendixRepository;

class ContractAppendixService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractAppendixRepository::class);
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
            'renewal_letter',
            'renewal_approval_letter',
            'renewal_appendix',
            'other_documents',
        ];

        $extracted = [];
        foreach ($fields as $field) {
            $extracted[$field] = $request[$field] ?? null;
            unset($request[$field]);
        }

        return $extracted;
    }

    private function handleFile(ContractAppendix $data, array $extracted, bool $isUpdate = false)
    {
        if ($extracted['renewal_letter']) {
            $oldFile = $isUpdate ? $data['renewal_letter'] : null;
            $data['renewal_letter'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['renewal_letter'], 'contract', 'appendix', $oldFile);
            $data->save();
        }
        if ($extracted['renewal_approval_letter']) {
            $oldFile = $isUpdate ? $data['renewal_approval_letter'] : null;
            $data['renewal_approval_letter'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['renewal_approval_letter'], 'contract', 'appendix', $oldFile);
            $data->save();
        }
        if ($extracted['renewal_appendix']) {
            $oldFile = $isUpdate ? $data['renewal_appendix'] : null;
            $data['renewal_appendix'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['renewal_appendix'], 'contract', 'appendix', $oldFile);
            $data->save();
        }
        if ($extracted['other_documents']) {
            $oldFile = $isUpdate ? $data['other_documents'] : null;
            $data['other_documents'] = $this->handlerUploadFileService->storeAndRemoveOld($extracted['other_documents'], 'contract', 'appendix', $oldFile);
            $data->save();
        }
    }

    protected function beforeDelete(int $id)
    {
        $data = $this->repository->findById($id, false);
        $this->handlerUploadFileService->removeFiles([
            $data['renewal_letter'],
            $data['renewal_approval_letter'],
            $data['renewal_appendix'],
            $data['other_documents'],
        ]);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['renewal_date'] = $this->formatDateForPreview($array['renewal_date']);
        $array['renewal_end_date'] = $this->formatDateForPreview($array['renewal_end_date']);
        $array['renewal_letter'] = $this->getAssetUrl($array['renewal_letter']);
        $array['renewal_approval_letter'] = $this->getAssetUrl($array['renewal_approval_letter']);
        $array['renewal_appendix'] = $this->getAssetUrl($array['renewal_appendix']);
        $array['other_documents'] = $this->getAssetUrl($array['other_documents']);
        return $array;
    }
}
