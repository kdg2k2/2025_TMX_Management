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
            $array['approved_at'] = $this->formatDateForPreview($array['approved_at']);
        if (isset($array['details']))
            $array['details'] = $this->trainAndBusTicketDetailService->formatRecords($array['details']);

        return $array;
    }

    public function baseDataForCreateView(int $id = null)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);
        $res['users'] = $this->userService->list([
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ]);
        $res['contracts'] = $this->contractService->list([
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ]);
        $res['types'] = $this->repository->getType();
        $res['userTypes'] = $this->trainAndBusTicketDetailService->repository->getUserType();
        return $res;
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $details = $request['details'] ?? [];
            unset($request['details']);

            $data = $this->repository->store($request);

            $this->syncDetails($data, $this->formatFields($data, $details));

            $this->sendMail($data['id'], 'Yều cầu');
        }, true);
    }

    private function formatFields(TrainAndBusTicket $data, array $fields)
    {
        $res = [];
        foreach ($fields as $key => $value) {
            $res[] = [
                'train_and_bus_ticket_id' => $data['id'],
                'user_type' => $value['user_type'],
                'user_id' => $value['user_id'],
                'external_user_name' => $value['external_user_name'],
                'created_by' => $data['created_by'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $res;
    }

    private function syncDetails(TrainAndBusTicket $data, array $pivot)
    {
        $data->details()->delete();

        if (count($pivot) == 0)
            return;

        $data->details()->insert($pivot);
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
            collect($data['details'])->pluck('user_id')->unique()->filter()->toArray()
        ]);
    }
}
