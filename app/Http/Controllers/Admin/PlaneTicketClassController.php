<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaneTicketClass\DeleteRequest;
use App\Http\Requests\PlaneTicketClass\EditRequest;
use App\Services\PlaneTicketClassService;

class PlaneTicketClassController extends Controller
{
    public function __construct()
    {
        $this->service = app(PlaneTicketClassService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.plane-ticket-class.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.plane-ticket-class.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.plane-ticket-class.edit', [
                'data' => $this->service->findById($request->validated()['id'])
            ]);
        });
    }

    public function delete(DeleteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->delete($request->validated()['id']);
            return response()->json([
                'message' => config('message.delete'),
            ]);
        });
    }
}
