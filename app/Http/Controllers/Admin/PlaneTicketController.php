<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlaneTicketService;
use App\Http\Requests\PlaneTicket\RejectRequest;
use App\Http\Requests\PlaneTicket\ApproveRequest;

class PlaneTicketController extends Controller
{
    public function __construct()
    {
        $this->service = app(PlaneTicketService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.plane-ticket.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.plane-ticket.create', $this->service->baseDataForCreateView());
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
