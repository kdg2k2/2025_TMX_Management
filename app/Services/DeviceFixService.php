<?php

namespace App\Services;

use App\Repositories\DeviceFixRepository;

class DeviceFixService extends BaseService
{
    public function __construct(
        private DeviceService $deviceService,
        private UserService $userService,
    ) {
        $this->repository = app(DeviceFixRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['approved_at']))
            $array['approved_at'] = $this->formatDateTimeForPreview($array['approved_at']);
        if (isset($array['fixed_at']))
            $array['fixed_at'] = $this->formatDateTimeForPreview($array['fixed_at']);

        return $array;
    }

    public function getBaseDataForLCView($listView = true)
    {
        $res = [
            'status' => $this->repository->getStatus(),
            'devices' => $this->deviceService->list(!$listView ? [
                'statuses' => [
                    'broken',
                    'faulty',
                    'loaned',
                    'stored',
                ]
            ] : []),
        ];

        if ($listView)
            $res['users'] = $this->userService->list([
                'load_relations' => false,
                'columns' => [
                    'id',
                    'name',
                ],
            ]);
        return $res;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            foreach ($request['details'] as $item) {
                $device = $this->deviceService->findById($item['device_id']);

                $data = $this->repository->store([
                    'created_by' => $request['created_by'],
                    'device_id' => $device['id'],
                    'suggested_content' => $item['suggested_content'],
                    'device_status' => $item['device_status'],
                    'device_status_upon_registration' => $device['current_status'],
                    'note' => $item['note'],
                ]);

                $device->update([
                    'current_status' => 'under_repair',
                ]);

                $this->sendMail($data['id'], 'Yêu cầu');
            }
        }, true);
    }

    public function approve(array $request)
    {
        return $this->handleAction($request, 'Phê duyệt');
    }

    public function reject(array $request)
    {
        return $this->handleAction(
            $request,
            'Từ chối',
            true
        );
    }

    public function fixed(array $request)
    {
        return $this->handleAction(
            $request,
            'Đã',
            true
        );
    }

    private function handleAction(array $request, string $mailTitle, bool $resetDeviceStatus = false)
    {
        return $this->tryThrow(function () use ($request, $mailTitle, $resetDeviceStatus) {
            $data = $this->repository->update($request);

            if ($resetDeviceStatus)
                $data->device()->update([
                    'current_status' => in_array($data['device_status_upon_registration'], [
                        'broken',
                        'faulty',
                    ]) ? 'normal' : $data['device_status_upon_registration'],
                ]);

            $this->sendMail($data['id'], $mailTitle);
        }, true);
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.device-fix', $subject . ' sửa chữa thiết bị', $emails, [
            'data' => $data,
        ]));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            collect([$data['created_by']['id'], $data['approved_by']['id'] ?? null])->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('DEVICE_FIX')
        ]);
    }

    public function remindFixDevice()
    {
        $data = $this->repository->list([
            'status' => 'approved',
        ]);

        if (count($data) == 0)
            return;

        foreach ($data as $item)
            $this->sendMail($item['id'], 'Nhắc nhở sửa thiết bị');

        return count($data);
    }
}
