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
            $this->handleExtracted($data, $extracted);
        }, true);
    }

    public function update(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $extracted = $this->extractFields($request);
            $data = $this->repository->update($request);
            $this->handleExtracted($data, $extracted, true);
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

    private function handleExtracted(ContractAppendix $data, array $extracted, bool $isUpdate = false)
    {
        $fields = [
            'renewal_letter',
            'renewal_approval_letter',
            'renewal_appendix',
            'other_documents',
        ];
        foreach ($fields as $field) {
            if ($extracted[$field]) {
                $oldFile = $isUpdate ? $data[$field] : null;
                $data[$field] = $this->handlerUploadFileService->storeAndRemoveOld($extracted[$field], 'contract', $field, $oldFile);
                $data->save();
            }
        }

        if ($isUpdate == false)
            $this->updateTimes($data['contract_id']);
    }

    private function updateTimes(int $contractId)
    {
        $list = $this->repository->list([
            'order_by' => 'id',
            'sort_by' => 'asc',
            'contract_id' => $contractId,
            'load_relations' => false,
        ]);

        if (count($list) == 0)
            return;

        $list->values()->each(function ($item, $index) {
            $item->update(['times' => $index + 1]);
        });
    }

    protected function afterDelete($entity)
    {
        $this->updateTimes($entity['contract_id']);

        $this->handlerUploadFileService->removeFiles([
            $entity['renewal_letter'],
            $entity['renewal_approval_letter'],
            $entity['renewal_appendix'],
            $entity['other_documents'],
        ]);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['renewal_date']))
            $array['renewal_date'] = $this->formatDateForPreview($array['renewal_date']);
        if (isset($array['renewal_end_date']))
            $array['renewal_end_date'] = $this->formatDateForPreview($array['renewal_end_date']);

        if (isset($array['renewal_letter']))
            $array['renewal_letter'] = $this->getAssetUrl($array['renewal_letter']);
        if (isset($array['renewal_approval_letter']))
            $array['renewal_approval_letter'] = $this->getAssetUrl($array['renewal_approval_letter']);
        if (isset($array['renewal_appendix']))
            $array['renewal_appendix'] = $this->getAssetUrl($array['renewal_appendix']);
        if (isset($array['other_documents']))
            $array['other_documents'] = $this->getAssetUrl($array['other_documents']);
        return $array;
    }
}
