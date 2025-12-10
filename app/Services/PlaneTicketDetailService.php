<?php

namespace App\Services;

use App\Repositories\PlaneTicketDetailRepository;

class PlaneTicketDetailService extends BaseService
{
    public function __construct()
    {
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
}
