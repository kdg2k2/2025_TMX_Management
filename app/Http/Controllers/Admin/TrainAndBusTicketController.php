<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrainAndBusTicketService;
use App\Http\Requests\TrainAndBusTicket\EditRequest;
use App\Http\Requests\TrainAndBusTicket\DeleteRequest;

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
            return view('admin.pages.train-and-bus-ticket.create', $this->service->baseDataForCreateEditView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.train-and-bus-ticket.edit', $this->service->baseDataForCreateEditView($request->validated()['id']));
        });
    }

    public function delete(DeleteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->delete($request->validated()['id']),
                'message' => config('message.delete'),
            ]);
        });
    }
}
