<?php

namespace App\Services;

use App\Repositories\ContractProductInspectionReporitory;

class ContractProductInspectionService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractProductInspectionReporitory::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['issue_file_path']))
            $array['issue_file_path'] = $this->getAssetUrl($array['issue_file_path']);
        return $array;
    }

    protected function beforeStore(array $request)
    {
        if (isset($request['issue_file_path']))
            $request['issue_file_path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['issue_file_path'], $this->repository->getTable());
        return $request;
    }

    public function cancel(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            $data->update($request);
            return $data;
        });
    }

    public function response(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            $data->update($request);
            return $data;
        });
    }
}
