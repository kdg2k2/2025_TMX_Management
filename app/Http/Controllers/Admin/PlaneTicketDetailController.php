<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PlaneTicketDetailService;
use App\Http\Requests\PlaneTicketDetail\EditRequest;

class PlaneTicketDetailController extends Controller
{
    public function __construct()
    {
        $this->service = app(PlaneTicketDetailService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.plane-ticket.detail.index');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.plane-ticket.detail.edit', $this->service->baseDataForEditView($request->validated()['id']));
        });
    }
}
