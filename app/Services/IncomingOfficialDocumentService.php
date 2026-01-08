<?php

namespace App\Services;

use App\Repositories\IncomingOfficialDocumentRepository;
use DateTime;

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

        foreach (['issuing_date', 'received_date'] as $field)
            if (!empty($array[$field]))
                $array[$field] = $this->formatDateForPreview($array[$field]);

        foreach (['assign_at', 'complete_at'] as $field)
            if (!empty($array[$field]))
                $array[$field] = $this->formatDateTimeForPreview($array[$field]);

        if (!empty($array['task_completion_deadline'])) {
            $array = array_merge($array, $this->checkDatelineExpired($array['task_completion_deadline']));
            $array['task_completion_deadline'] = $this->formatDateForPreview($array['task_completion_deadline']);
        }

        if (!empty($array['attachment_file']))
            $array['attachment_file'] = $this->getAssetUrl($array['attachment_file']);

        return $array;
    }

    public function checkDatelineExpired(string $date)
    {
        $deadline = new DateTime($date);
        $today = new DateTime();

        $diff = $today->diff($deadline)->days;
        $expired = $deadline < $today;

        return [
            'expired' => $expired,
            'expired_color' => $expired ? 'danger' : ($diff >= 3 ? 'primary' : 'warning'),
            'expired_message' => $expired ? 'Đã quá hạn' : ($diff >= 3 ? 'Còn thời gian' : 'Sắp đến hạn'),
        ];
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
        $res['status'] = $this->repository->getStatus();
        return $res;
    }

    public function assign(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $users = $request['users'] ?? [];
            unset($request['users']);
            $data = $this->repository->update($request);
            $data->users()->sync($users);
            
            return $data;
        }, true);
    }

    public function complete(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            return $this->repository->update($request);
        }, true);
    }
}
