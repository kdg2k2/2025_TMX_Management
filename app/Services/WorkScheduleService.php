<?php

namespace App\Services;

use App\Models\WorkSchedule;
use App\Repositories\WorkScheduleRepository;
use Arr;
use Exception;

class WorkScheduleService extends BaseService
{
    public function __construct(
        private DateService $dateService,
        private ContractService $contractService,
        private UserService $userService,
    ) {
        $this->repository = app(WorkScheduleRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['type_program'] = $this->repository->getTypeProgram($array['type_program']);
        $array['approval_status'] = $this->repository->getApprovalStatus($array['approval_status']);
        $array['return_approval_status'] = $this->repository->getReturnApprovalStatus($array['return_approval_status']);
        $array['is_completed'] = $this->repository->getIsCompleted($array['is_completed']);

        if (isset($array['from_date']))
            $array['from_date'] = $this->formatDateForPreview($array['from_date']);
        if (isset($array['to_date']))
            $array['to_date'] = $this->formatDateForPreview($array['to_date']);
        if (isset($array['approval_date']))
            $array['approval_date'] = $this->formatDateForPreview($array['approval_date']);
        if (isset($array['return_datetime']))
            $array['return_datetime'] = $this->formatDateTimeForPreview($array['return_datetime']);
        if (isset($array['return_approval_date']))
            $array['return_approval_date'] = $this->formatDateForPreview($array['return_approval_date']);

        foreach ([
            'createdBy',
            'approvedBy',
            'returnApprovedBy',
        ] as $item) {
            if (isset($array[$item]))
                $array[$item] = $this->userService->formatRecord($array[$item]);
        }

        return $array;
    }

    public function getTotalTripDays(string $from, string $to)
    {
        return $this->dateService->getTotalDays($from, $to);
    }

    public function isPendingApproval(WorkSchedule $data)
    {
        if ($data['approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt công tác');
    }

    public function isApproved(WorkSchedule $data)
    {
        if ($data['approval_status'] != 'approved')
            throw new Exception('Bản ghi đang không ở trạng thái đã duyệt công tác');
    }

    public function isPendingReturnApproval(WorkSchedule $data)
    {
        if ($data['return_approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt về');
    }

    public function baseDataList()
    {
        return array_merge(
            [
                'users' => $this->userService->list([
                    'columns' => ['id', 'name'],
                ]),
                'approvalStatus' => $this->repository->getApprovalStatus(),
                'returnApprovalStatus' => $this->repository->getReturnApprovalStatus(),
                'isCompleted' => $this->repository->getIsCompleted(),
            ],
            $this->baseDataCreate()
        );
    }

    public function baseDataCreate()
    {
        return [
            'contracts' => $this->contractService->list([
                'columns' => ['id', 'name'],
            ]),
            'typeProgram' => $this->repository->getTypeProgram(),
        ];
    }

    public function getOverlappedDays(int $userId, string $from, string $to)
    {
        // Lấy các lịch có giao khoảng
        $records = $this->repository->getUserWorkScheduleFromTo($userId, $from, $to);

        $overlapped = collect();

        // Duyệt qua từng lịch trùng
        foreach ($records as $record) {
            // Lấy danh sách ngày của lịch cũ
            $existingDays = $this->dateService->getDatesInRange($record->from_date, $record->to_date);
            // Lấy danh sách ngày của lịch mới
            $newDays = $this->dateService->getDatesInRange($from, $to);

            // Lấy giao giữa 2 danh sách
            $intersection = $existingDays->intersect($newDays);

            if ($intersection->isNotEmpty()) {
                $overlapped = $overlapped->merge($intersection);
            }
        }

        // Trả về các ngày trùng (bỏ trùng lặp)
        return $overlapped->unique()->values()->toArray();
    }

    public function checkOverLapDays(array $request)
    {
        $overlapDays = $this->getOverlappedDays($request['created_by'], $request['from_date'], $request['to_date']);
        if (count($overlapDays) > 0)
            throw new Exception('Bị trùng lịch công tác các ngày: ' . implode(' - ', array_map(fn($i) =>
                $this->formatDateForPreview($i), $overlapDays)));
    }

    public function beforeStore(array $request)
    {
        $this->checkOverLapDays($request);
        app(LeaveRequestService::class)->checkOverLapDays($request);

        $request['total_trip_days'] = $this->getTotalTripDays($request['from_date'], $request['to_date']);

        return $request;
    }

    protected function afterStore($data, array $request)
    {
        $this->sendMail($data['id'], 'Yêu cầu phê duyệt');
    }

    public function approvalRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isPendingApproval($data);

            $data->update($request);

            $this->sendMail($data['id'],
                $request['approval_status'] == 'approved' ? 'Phê duyệt' : 'Từ chối');
        }, true);
    }

    public function returnRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isApproved($data);

            if ($this->getGuard()->user()->id != $data['created_by'])
                throw new Exception('Chỉ người đăng ký mới có thể yêu cầu kết thúc lịch công tác này');

            $data->update([
                'return_datetime' => $request['return_datetime'],
                'return_approval_status' => 'pending',
                'return_approval_note' => null,
                'return_approval_date' => null,
                'return_approved_by' => null,
            ]);

            $this->sendMail($data['id'], 'Yêu cầu kết thúc');
        }, true);
    }

    public function returnApprovalRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isApproved($data);
            $this->isPendingReturnApproval($data);

            $data->update($request);

            $this->sendMail($data['id'],
                $request['return_approval_status'] == 'approved' ? 'Phê duyệt yêu cầu kết thúc' : 'Từ chối yêu cầu kết thúc');

            if ($request['return_approval_status'] == 'approved')
                $this->setComplete($data);
        }, true);
    }

    public function setComplete(WorkSchedule $data)
    {
        return $this->tryThrow(function () use ($data) {
            $this->isApproved($data);

            $params = [
                'is_completed' => true,
                'total_trip_days' => $this->getTotalTripDays($data['from_date'], $data['to_date']),
                'total_work_days' => $this->dateService->getTotalDays($data['from_date'], $data['to_date'], [0]),
            ];

            if ($data['return_approval_status'] == 'pendding')
                $params['return_approval_status'] = 'none';

            $data->update($params);

            $this->sendMail($data['id'], 'Đã kết thúc');
        }, true);
    }

    private function getEmails(WorkSchedule $data)
    {
        $contractEmails = [];
        if ($data['contract_id'])
            $contractEmails = $this->contractService->getMemberEmails($data['contract_id'], [
                'executor_user',
                'instructors',
                'professionals',
            ]);

        $recordMemberEmails = $this->userService->getEmails(array_merge(
            $this->userService->getUserDepartmentManagerEmail($data['createdBy']['id'] ?? null), [
                $data['createdBy']['id'] ?? null,
                $data['approvedBy']['id'] ?? null,
                $data['returnApprovedBy']['id'] ?? null,
            ]
        ));

        return Arr::flatten(array_merge($contractEmails,
            $recordMemberEmails));
    }

    private function sendMail(int $id, string $subject)
    {
        $record = $this->repository->findById($id);
        $emails = $this->getEmails($record);
        dispatch(new \App\Jobs\SendMailJob('emails.work-schedule', $subject . ' lịch công tác', $emails, [
            'data' => $this->formatRecord($record->toArray()),
        ]));
    }

    public function setCompletedWorkSchedules()
    {
        return $this->tryThrow(function () {
            $list = $this->repository->list([
                'is_completed' => false,
                'approval_status' => 'approved',
            ]);
            $today = date('Y-m-d');
            foreach ($list as $item) {
                if ($item['to_date'] == $today)
                    $this->setComplete($item);
            }
        });
    }

    public function getBaseQueryForDateRange(string $from, string $to)
    {
        return $this->tryThrow(function () use ($from, $to) {
            return $this->repository->getBaseQueryForDateRange($from, $to);
        });
    }
}
