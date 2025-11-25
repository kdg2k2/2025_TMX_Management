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
}
