<?php

namespace App\Services;

use App\Repositories\TrainAndBusTicketDetailRepository;

class TrainAndBusTicketDetailService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(TrainAndBusTicketDetailRepository::class);
    }

    public function baseDataForEditView(int $id)
    {
        $res = [];
        if ($id)
            $res['data'] = $this->repository->findById($id);
        $res['userTypes'] = $this->repository->getUserType();
        return $res;
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);

        $array['user_type'] = $this->repository->getUserType($array['user_type']);
        if (isset($array['ticket_image_path']))
            $array['ticket_image_path'] = $this->getAssetUrl($array['ticket_image_path']);
        if (isset($array['departure_date']))
            $array['departure_date'] = $this->formatDateForPreview($array['departure_date']);
        if (isset($array['return_date']))
            $array['return_date'] = $this->formatDateForPreview($array['return_date']);

        return $array;
    }

    public function update(array $request)
    {
        $oldTicketImagePath = null;
        if (isset($request['ticket_image_path'])) {
            $data = $this->repository->findById($request['id'], false);
            $oldTicketImagePath = $data['ticket_image_path'];
            $request['ticket_image_path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['ticket_image_path'], 'train-and-bus-tickets');
        }

        $data = $this->repository->update($request);

        $this->handlerUploadFileService->removeFiles($oldTicketImagePath);

        return $data;
    }

    public function getUserType($key = null)
    {
        return $this->repository->getUserType($key);
    }
}
