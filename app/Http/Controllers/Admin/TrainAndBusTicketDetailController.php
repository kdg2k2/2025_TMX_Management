<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrainAndBusTicketDetailService;
use App\Http\Requests\TrainAndBusTicketDetail\EditRequest;

class TrainAndBusTicketDetailController extends Controller
{
    public function __construct()
    {
        $this->service = app(TrainAndBusTicketDetailService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.train-and-bus-ticket.detail.index');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.train-and-bus-ticket.detail.edit', $this->service->baseDataForEditView($request->validated()['id']));
        });
    }
}
