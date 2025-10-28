<?php

namespace App\Services;

use App\Models\WorkSchedule;
use App\Repositories\WorkScheduleRepository;
use Exception;

class WorkScheduleService extends BaseService
{
    public function __construct(
        private DateService $dateService,
        private ContractService $contractService,
        private UserService $userService
    ) {
        $this->repository = app(WorkScheduleRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['type_program'] = $this->getTypeProgram($array['type_program']);
        $array['approval_status'] = $this->getApprovalStatus($array['approval_status']);
        $array['return_approval_status'] = $this->getReturnApprovalStatus($array['return_approval_status']);

        return $array;
    }

    public function getTypeProgram($key = null)
    {
        return $this->repository->getTypeProgram($key);
    }

    public function getApprovalStatus($key = null)
    {
        return $this->repository->getApprovalStatus($key);
    }

    public function getReturnApprovalStatus($key = null)
    {
        return $this->repository->getReturnApprovalStatus($key);
    }

    public function getTotalTripDays(string $from, string $to)
    {
        return $this->dateService->getTotalDays($from, $to);
    }

    public function isPendingApproval(WorkSchedule $data)
    {
        if ($data['approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt');
    }

    public function isApproved(WorkSchedule $data)
    {
        if ($data['approval_status'] != 'approved')
            throw new Exception('Bản ghi đang không ở trạng thái đã duyệt');
    }

    public function isPendingReturnApproval(WorkSchedule $data)
    {
        if ($data['return_approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt');
    }

    public function baseDataList()
    {
        return [
            'contracts' => $this->contractService->list([
                'columns' => ['id', 'name'],
            ]),
            'users' => $this->userService->list([
                'columns' => ['id', 'name'],
            ]),
            'typeProgram' => $this->getTypeProgram(),
            'approvalStatus' => $this->getApprovalStatus(),
            'returnApprovalStatus' => $this->getReturnApprovalStatus(),
            'isCompleted' => $this->repository->getIsCompleted(),
        ];
    }

    public function baseDataCreate()
    {
        return [
            'contracts' => $this->contractService->list([
                'columns' => ['id', 'name'],
            ]),
        ];
    }

    public function approvalRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isPendingApproval($data);

            $data->update($request);
        }, true);
    }

    public function returnRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isApproved($data);

            $data->update([
                'return_datetime' => $request['return_datetime'],
                'return_approval_status' => 'pending',
                'return_approval_note' => null,
                'return_approval_date' => null,
                'return_approved_by' => null,
            ]);
        }, true);
    }

    public function returnApprovalRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isPendingApproval($data);
            $this->isPendingReturnApproval($data);

            $data->update($request);
        }, true);
    }

    public function completeRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isApproved($data);

            $data->update([
                'is_completed' => true,
                'total_trip_days' => $this->getTotalTripDays($data['from_date'], $data['to_date']),
                'total_work_days' => $this->dateService->getTotalDays($data['from_date'], $data['to_date'], [0]),
            ]);
        }, true);
    }
}
