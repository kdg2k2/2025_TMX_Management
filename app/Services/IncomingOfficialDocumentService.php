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

        foreach (['issuing_date', 'received_date'] as $field)
            if (isset($array[$field]))
                $array[$field] = $this->formatDateForPreview($array[$field]);

        foreach (['assign_at', 'complete_at'] as $field)
            if (isset($array[$field]))
                $array[$field] = $this->formatDateTimeForPreview($array[$field]);

        if (isset($array['task_completion_deadline'])) {
            if ($array['status'] == 'in_progress')
                $array = array_merge($array, $this->checkDatelineExpired($array['task_completion_deadline']));
            $array['task_completion_deadline'] = $this->formatDateForPreview($array['task_completion_deadline']);
        }

        if (isset($array['attachment_file']))
            $array['attachment_file'] = $this->getAssetUrl($array['attachment_file']);

        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);

        if (isset($array['program_type']))
            $array['name'] = $array['program_type'] == 'contract' ? ($array['contract']['name'] ?? null) : $array['other_program_name'];

        return $array;
    }

    public function checkDatelineExpired(string $date): array
    {
        $deadline = new DateTime($date);
        $today = new DateTime('today');

        $interval = $today->diff($deadline);
        $days = $interval->days;
        $expired = $deadline < $today;

        if ($expired) {
            return [
                'expired' => true,
                'expired_color' => 'danger',
                'expired_message' => "Quá hạn {$days} ngày",
                'days' => -$days,
            ];
        }

        if ($days >= 3) {
            return [
                'expired' => false,
                'expired_color' => 'primary',
                'expired_message' => "Còn {$days} ngày",
                'days' => $days,
            ];
        }

        return [
            'expired' => false,
            'expired_color' => 'warning',
            'expired_message' => "Sắp đến hạn (còn {$days} ngày)",
            'days' => $days,
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
            $this->sendMail($data['id'], 'Giao');
            return $data;
        }, true);
    }

    public function complete(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Hoàn thành');
            return $data;
        }, true);
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, false);
        $files = [$this->handlerUploadFileService->getAbsolutePublicPath($data['attachment_file'])];
        $data = $this->formatRecord($data->load($this->repository->relations)->toArray());
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.incoming-official-document', $subject . ' nhiệm vụ từ văn bản đến', $emails, [
            'data' => $data,
        ], $files));
    }

    public function getEmails(array $data)
    {
        return $this->userService->getEmails([
            $data['created_by']['id'],
            $data['task_assignee_id']['id'] ?? null,
            $data['assinged_by']['id'] ?? null,
            $data['contract_id'] ? $this->contractService->getMemberEmails($data['contract_id'], [
                'executor_user',
                'instructors',
                'professionals',
            ]) : [],
            collect($data['incoming_official_document_users'])->pluck('user_id')->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('INCOMING_OFFICIAL_DOCUMENT')
        ]);
    }
}
