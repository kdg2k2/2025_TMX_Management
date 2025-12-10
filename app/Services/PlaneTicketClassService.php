<?php

namespace App\Services;

use App\Repositories\PlaneTicketClassRepository;

class PlaneTicketClassService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(PlaneTicketClassRepository::class);
    }
}
