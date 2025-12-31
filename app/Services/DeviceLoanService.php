<?php

namespace App\Services;

use App\Repositories\DeviceLoanRepository;
use Exception;

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
        if (isset($array['device_status_return']))
            $array['device_status_return'] = $this->repository->getStatusReturn($array['device_status_return']);
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
        return [
            'status' => $this->repository->getStatus(),
            'statusReturn' => $this->repository->getStatusReturn(),
            'users' => $this->userService->list([
                'load_relations' => false,
                'columns' => ['id', 'name'],
            ]),
            'devices' => $this->deviceService->list(!$listView ? [
                'current_status' => 'normal'
            ] : []),
        ];
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            foreach ($request['details'] as $item) {
                $data = $this->repository->store([
                    'created_by' => $request['created_by'],
                    'borrowed_date' => $request['borrowed_date'],
                    'device_id' => $item['device_id'],
                    'expected_return_at' => $item['expected_return_at'],
                    'use_location' => $item['use_location'],
                    'note' => $item['note'],
                ]);

                $data->device()->update([
                    'current_status' => 'loaned',
                    'user_id' => $request['created_by'],
                    'current_location' => $item['use_location'],
                ]);

                $this->sendMail($data['id'], 'Yêu cầu mượn');
            }
        }, true);
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt mượn');
        }, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $data->device()->update([
                'current_status' => 'normal',
                'user_id' => null,
                'current_location' => null,
            ]);
            $this->sendMail($data['id'], 'Từ chối mượn');
        }, true);
    }

    public function return(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            if (!in_array($this->getUserId(), [1, $data['created_by']]))
                throw new Exception('Chỉ chính chủ mới có thể trả đồ!');
            $data->update($request);
            $data->device()->update([
                'current_status' => $request['device_status_return'],
                'user_id' => null,
                'current_location' => null,
            ]);
            $this->sendMail($data['id'], 'Trả');
        }, true);
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob(
            'emails.device-loan',
            $subject . ' thiết bị',
            $emails,
            [
                'data' => $data,
            ]
        ));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            collect([$data['created_by']['id'], $data['approved_by']['id'] ?? null])->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('DEVICE_LOAN')
        ]);
    }

    public function remindReturnDevice()
    {
        $data = $this->repository->getOverdueApprovedLoans();

        if (count($data) == 0)
            return;

        foreach ($data as $item)
            $this->sendMail($item['id'], 'Nhắc nhở trả thiết bị');

        return count($data);
    }

    public function statistic(array $request)
    {
        return $this->repository->statistic($request);
    }

    public function statisticByMonth(array $request)
    {
        return $this->repository->statisticByMonth($request);
    }
}
