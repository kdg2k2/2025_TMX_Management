<?php

namespace App\Services;

use App\Repositories\PlaneTicketDetailRepository;

class PlaneTicketDetailService extends BaseService
{
    public function __construct(
        private PlaneTicketClassService $planeTicketClassService,
        private AirlineService $airlineService,
        private AirportService $airportService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(PlaneTicketDetailRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['user_type']))
            $array['user_type'] = $this->repository->getUserType($array['user_type']);
        if (isset($array['ticket_image_path']))
            $array['ticket_image_path'] = $this->getAssetUrl($array['ticket_image_path']);
        if (isset($array['departure_date']))
            $array['departure_date'] = $this->formatDateForPreview($array['departure_date']);
        if (isset($array['return_date']))
            $array['return_date'] = $this->formatDateForPreview($array['return_date']);
        return $array;
    }

    public function getUserType($key = null)
    {
        return $this->repository->getUserType($key);
    }

    public function baseDataForEditView(int $id = null)
    {
        $baseInfo = [
            'load_relations' => false,
            'columns' => ['id', 'name'],
        ];
        return [
            'data' => $this->repository->findById($id),
            'userTypes' => $this->repository->getUserType(),
            'airports' => $this->airportService->list($baseInfo),
            'airlines' => $this->airlineService->list($baseInfo),
            'planeTicketClasses' => $this->planeTicketClassService->list($baseInfo),
        ];
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
}
