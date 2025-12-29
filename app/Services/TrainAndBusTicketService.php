<?php

namespace App\Services;

use App\Models\TrainAndBusTicket;
use App\Repositories\TrainAndBusTicketRepository;

class TrainAndBusTicketService extends BaseService
{
    public function __construct(
        private UserService $userService,
        private ContractService $contractService,
        private TrainAndBusTicketDetailService $trainAndBusTicketDetailService
    ) {
        $this->repository = app(TrainAndBusTicketRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['type'] = $this->repository->getType($array['type']);
        $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['estimated_travel_time']))
            $array['estimated_travel_time'] = $this->formatDateForPreview($array['estimated_travel_time']);
        if (isset($array['approved_at']))
            $array['approved_at'] = $this->formatDateTimeForPreview($array['approved_at']);
        if (isset($array['details']))
            $array['details'] = $this->trainAndBusTicketDetailService->formatRecords($array['details']);

        return $array;
    }

    public function baseDataForCreateView()
    {
        $baseInfo = [
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ];
        return [
            'userTypes' => $this->trainAndBusTicketDetailService->getUserType(),
            'types' => $this->repository->getType(),
            'contracts' => $this->contractService->list($baseInfo),
            'users' => $this->userService->list($baseInfo),
        ];
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $details = $request['details'] ?? [];
            unset($request['details']);

            $data = $this->repository->store($request);

            $this->syncDetails($data, $details);

            $this->sendMail($data['id'], 'Yều cầu');
        }, true);
    }

    private function syncDetails(TrainAndBusTicket $data, array $details)
    {
        $this->syncRelationship($data, 'train_and_bus_ticket_id', 'details', array_map(function ($i) use ($data) {
            $i['created_by'] = $data['created_by'];
            return $i;
        }, $details));
    }

    public function approve(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Phê duyệt');
        }, true);
    }

    public function reject(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->update($request);
            $this->sendMail($data['id'], 'Từ chối');
        }, true);
    }

    private function sendMail(int $id, string $subject)
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.train-and-bus-ticket', $subject . ' đăng ký vé tàu xe', $emails, [
            'data' => $data,
        ]));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            $data['created_by']['id'],
            $data['approved_by']['id'] ?? null,
            collect($data['details'])->pluck('user_id')->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('TRAIN_AND_BUS_TICKET')
        ]);
    }
}
