<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\DeleteRequest;
use App\Http\Requests\Device\EditRequest;
use App\Services\DeviceService;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.index', $this->service->getBaseDataForLCEView(null, true));
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.create', $this->service->getBaseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.device.edit', $this->service->getBaseDataForLCEView($request->validated()['id']));
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
