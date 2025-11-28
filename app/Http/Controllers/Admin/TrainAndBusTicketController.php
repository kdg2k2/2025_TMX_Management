<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TrainAndBusTicketService;
use App\Http\Requests\TrainAndBusTicket\RejectRequest;
use App\Http\Requests\TrainAndBusTicket\ApproveRequest;

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

    public function approve(ApproveRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->approve($request->validated()),
            'message' => config('message.approve'),
        ]));
    }

    public function reject(RejectRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->reject($request->validated()),
            'message' => config('message.approve'),
        ]));
    }
}
