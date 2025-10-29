<?php

namespace App\Services;

use App\Models\LeaveRequest;
use App\Repositories\LeaveRequestRepository;
use Arr;
use Exception;

class LeaveRequestService extends BaseService
{
    public function __construct(
        private DateService $dateService,
        private UserService $userService
    ) {
        $this->repository = app(LeaveRequestRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['approval_status'] = $this->repository->getApprovalStatus($array['approval_status']);
        $array['adjust_approval_status'] = $this->repository->getAdjustApprovalStatus($array['adjust_approval_status']);

        if (isset($array['from_date']))
            $array['from_date'] = $this->formatDateForPreview($array['from_date']);
        if (isset($array['to_date']))
            $array['to_date'] = $this->formatDateForPreview($array['to_date']);
        if (isset($array['approval_date']))
            $array['approval_date'] = $this->formatDateForPreview($array['approval_date']);
        if (isset($array['adjust_approval_date']))
            $array['adjust_approval_date'] = $this->formatDateForPreview($array['adjust_approval_date']);

        foreach ([
            'createdBy',
            'approvedBy',
            'adjustApprovedBy',
        ] as $item) {
            if (isset($array[$item]))
                $array[$item] = $this->userService->formatRecord($array[$item]);
        }

        return $array;
    }

    public function getTotalLeaveDays(string $from, string $to, string $type = 'one_day')
    {
        $ignoreDays = [0];  // Bỏ qua chủ nhật

        return match ($type) {
            'morning' => $this->dateService->getTotalDaysDecimal(
                $from,
                $to,
                'morning',
                'morning',
                $ignoreDays
            ),
            'afternoon' => $this->dateService->getTotalDaysDecimal(
                $from,
                $to,
                'afternoon',
                'afternoon',
                $ignoreDays
            ),
            'one_day', 'many_days' => $this->dateService->getTotalDaysDecimal(
                $from,
                $to,
                'morning',
                'afternoon',
                $ignoreDays
            ),
            default => 0
        };
    }

    public function isPendingApproval(LeaveRequest $data)
    {
        if ($data['approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt nghỉ phép');
    }

    public function isApproved(LeaveRequest $data)
    {
        if ($data['approval_status'] != 'approved')
            throw new Exception('Bản ghi đang không ở trạng thái đã duyệt nghỉ phép');
    }

    public function isPendingAdjustApproval(LeaveRequest $data)
    {
        if ($data['adjust_approval_status'] != 'pending')
            throw new Exception('Bản ghi đang không ở trạng thái chờ duyệt điều chỉnh');
    }

    public function baseDataList()
    {
        return array_merge(
            [
                'users' => $this->userService->list([
                    'columns' => ['id', 'name'],
                ]),
                'approvalStatus' => $this->repository->getApprovalStatus(),
                'adjustApprovalStatus' => $this->repository->getAdjustApprovalStatus(),
            ],
            $this->baseDataCreateAndAdjust()
        );
    }

    public function baseDataCreateAndAdjust(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);

        $res['types'] = $this->repository->getType();

        return $res;
    }

    public function getOverlappedDays(int $userId, string $from, string $to, int $ignoreId = null)
    {
        // Lấy các lịch có giao khoảng
        $records = $this->repository->getUserLeaveFromTo($userId, $from, $to, $ignoreId);

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

    private function checkOverLapDays(array $request)
    {
        $overlapDays = $this->getOverlappedDays($request['created_by'], $request['from_date'], $request['to_date'], $request['id'] ?? null);
        if (count($overlapDays) > 0)
            throw new Exception('Bị trùng lịch nghỉ các ngày: ' . implode(' - ', array_map(fn($i) =>
                $this->formatDateForPreview($i), $overlapDays)));
    }

    public function beforeStore(array $request)
    {
        $this->checkOverLapDays($request);

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

    public function adjustRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id'], false);
            $this->isApproved($data);

            if ($this->getGuard()->user()->id != $data['created_by'])
                throw new Exception('Chỉ người đăng ký mới có thể điều chỉnh');

            $oldData = collect($data)->toArray();
            $this->checkOverLapDays($oldData);

            $data->update(array_merge($request, [
                'adjust_approval_status' => 'pending',
                'adjust_approval_note' => null,
                'adjust_approval_date' => null,
                'adjust_approved_by' => null,
                'before_adjust' => json_encode($oldData, JSON_UNESCAPED_UNICODE)
            ]));

            $this->sendMail($data['id'], 'Yêu cầu điều chỉnh');
        }, true);
    }

    public function adjustApprovalRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->findById($request['id']);
            $this->isApproved($data);
            $this->isPendingAdjustApproval($data);

            $subject = 'Phê duyệt yêu cầu điều chỉnh';
            if ($request['adjust_approval_status'] == 'rejected') {
                $subject = 'Từ chối yêu cầu điều chỉnh';

                $oldData = json_decode($data['before_adjust'], true);
                $ignoreKeys = array_merge(
                    [
                        'created_at',
                        'updated_at',
                    ],
                    array_keys($request)
                );
                $oldData = array_diff_key($oldData, array_flip($ignoreKeys));
                $request = array_merge($request, $oldData);
            }
            $data->update($request);

            $this->sendMail($data['id'], $subject);
        }, true);
    }

    private function getEmails(LeaveRequest $data)
    {
        $recordMemberEmails = $this->userService->getEmails(array_merge(
            $this->userService->getUserDepartmentManagerEmail($data['createdBy']['id'] ?? null), [
                $data['createdBy']['id'] ?? null,
                $data['approvedBy']['id'] ?? null,
                $data['adjustApprovedBy']['id'] ?? null,
            ]
        ));

        return Arr::flatten($recordMemberEmails);
    }

    private function sendMail(int $id, string $subject)
    {
        $record = $this->repository->findById($id);
        $emails = $this->getEmails($record);
        dispatch(new \App\Jobs\SendMailJob('emails.leave-request', $subject . ' nghỉ phép', $emails, [
            'data' => $this->formatRecord($record->toArray()),
        ]));
    }
}
