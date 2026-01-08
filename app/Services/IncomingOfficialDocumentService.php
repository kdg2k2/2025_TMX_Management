<?php

namespace App\Services;

use App\Repositories\IncomingOfficialDocumentRepository;

class IncomingOfficialDocumentService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private OfficialDocumentTypeService $officialDocumentTypeService,
        private UserService $userService,
        private ContractService $contractService
    ) {
        $this->repository = app(IncomingOfficialDocumentRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['issuing_date']))
            $array['issuing_date'] = $this->formatDateForPreview($array['issuing_date']);
        if (isset($array['received_date']))
            $array['received_date'] = $this->formatDateForPreview($array['received_date']);
        if (isset($array['task_completion_deadline']))
            $array['task_completion_deadline'] = $this->formatDateForPreview($array['task_completion_deadline']);
        if (isset($array['attachment_file']))
            $array['attachment_file'] = $this->getAssetUrl($array['attachment_file']);
        return $array;
    }

    public function beforeStore(array $request)
    {
        $request['attachment_file'] = $this->handlerUploadFileService->storeAndRemoveOld($request['attachment_file'], $this->repository->model->getTable());
        return $request;
    }

    public function beforeUpdate(array $request)
    {
        if (isset($request['attachment_file']))
            $request['attachment_file'] = $this->handlerUploadFileService->storeAndRemoveOld($request['attachment_file'], $this->repository->model->getTable(), null, $this->repository->findById($request['id'], false)['attachment_file']);
        return $request;
    }

    public function afterDelete($entity)
    {
        $this->handlerUploadFileService->removeFiles($entity['attachment_file']);
    }

    public function getBaseDataForLCEView(int $id = null)
    {
        $res = [];
        $baseRequest = [
            'load_relations' => false,
            'columns' => [
                'id',
                'name',
            ]
        ];
        if ($id)
            $res['data'] = $this->repository->findById($id);
        $res['officialDocumentTypes'] = $this->officialDocumentTypeService->list($baseRequest);
        $res['users'] = $this->userService->list($baseRequest);
        $res['programTypes'] = $this->repository->getProgramType();
        $res['contracts'] = $this->contractService->list($baseRequest);
        return $res;
    }
}
