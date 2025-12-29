<?php

namespace App\Services;

use App\Models\PlaneTicket;
use App\Repositories\PlaneTicketRepository;

class PlaneTicketService extends BaseService
{
    public function __construct(
        private PlaneTicketDetailService $planeTicketDetailService,
        private PlaneTicketClassService $planeTicketClassService,
        private AirlineService $airlineService,
        private AirportService $airportService,
        private UserService $userService,
        private ContractService $contractService
    ) {
        $this->repository = app(PlaneTicketRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['type']))
            $array['type'] = $this->repository->getType($array['type']);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['estimated_flight_time']))
            $array['estimated_flight_time'] = $this->formatDateTimeForPreview($array['estimated_flight_time']);
        if (isset($array['approved_at']))
            $array['approved_at'] = $this->formatDateTimeForPreview($array['approved_at']);
        if (isset($array['details']))
            $array['details'] = $this->planeTicketDetailService->formatRecords($array['details']);

        return $array;
    }

    public function baseDataForCreateView()
    {
        $baseInfo = [
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ];
        return [
            'userTypes' => $this->planeTicketDetailService->getUserType(),
            'types' => $this->repository->getType(),
            'contracts' => $this->contractService->list($baseInfo),
            'users' => $this->userService->list($baseInfo),
            'airports' => $this->airportService->list($baseInfo),
            'airlines' => $this->airlineService->list($baseInfo),
            'planeTicketClasses' => $this->planeTicketClassService->list($baseInfo),
        ];
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $details = $request['details'] ?? null;
            unset($request['details']);
            $data = $this->repository->store($request);
            $this->syncDetails($data, $details);
            $this->sendMail($data['id'], 'Yều cầu');
        }, true);
    }

    private function syncDetails(PlaneTicket $data, array $details)
    {
        $this->syncRelationship($data, 'plane_ticket_id', 'details', array_map(function ($i) use ($data) {
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
        dispatch(new \App\Jobs\SendMailJob('emails.plane-ticket', $subject . ' đăng ký vé máy bay', $emails, [
            'data' => $data,
        ]));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            $data['created_by']['id'],
            $data['approved_by']['id'] ?? null,
            collect($data['details'])->pluck('user_id')->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('PLANE_TICKET')
        ]);
    }
}
