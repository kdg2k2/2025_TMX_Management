<?php

namespace App\Services;

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

        return $array;
    }

    public function baseDataForCreateEditView(int $id = null)
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
}
