<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrainAndBusTicketService;

class TrainAndBusTicketController extends Controller
{
    public function __construct()
    {
        $this->service = app(TrainAndBusTicketService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.train-and-bus-ticket.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.train-and-bus-ticket.create', $this->service->baseDataForCreateView());
        });
    }
}
