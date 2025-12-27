<?php

namespace App\Services;

use App\Repositories\DeviceLoanRepository;

class DeviceLoanService extends BaseService
{
    public function __construct(
        private DeviceService $deviceService,
        private UserService $userService,
    ) {
        $this->repository = app(DeviceLoanRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['borrowed_date']))
            $array['borrowed_date'] = $this->formatDateForPreview($array['borrowed_date']);
        if (isset($array['expected_return_at']))
            $array['expected_return_at'] = $this->formatDateForPreview($array['expected_return_at']);
        if (isset($array['approved_at']))
            $array['approved_at'] = $this->formatDateTimeForPreview($array['approved_at']);
        if (isset($array['returned_at']))
            $array['returned_at'] = $this->formatDateTimeForPreview($array['returned_at']);

        return $array;
    }

    public function getBaseDataForLCView($listView = true)
    {
        $baseInfo = [
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ];

        $deviceFilters = [
            ...$baseInfo,
            'custom_relations' => ['deviceType:id,name'],
        ];

        if (!$listView)
            $deviceFilters['status'] = 'normal';

        return [
            'status' => $this->repository->getStatus(),
            'devices' => $this->deviceService->list($deviceFilters),
            'users' => $this->userService->list($baseInfo),
        ];
    }

    public function afterStore($data, array $request)
    {
        $this->sendMail($data['id'], 'Yêu cầu mượn');
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt mượn');
        });
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Từ chối mượn');
        });
    }

    public function return(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Trả');
        });
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.device-loan', $subject . ' thiết bị', $emails, [
            'data' => $data,
        ]));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            collect([$data['created_by'], $data['approved_by']])->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->findByKey('DEVICE_LOAN', 'code', false, false, ['emails'])['emails']->pluck('user_id')->unique()->filter()->toArray()
        ]);
    }
}
