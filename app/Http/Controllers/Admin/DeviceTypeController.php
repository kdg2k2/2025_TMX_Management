<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceType\DeleteRequest;
use App\Http\Requests\DeviceType\EditRequest;
use App\Http\Requests\DeviceType\ImportRequest;
use App\Services\DeviceTypeService;

class DeviceTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.type.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.device.type.edit', [
                'data' => $this->service->findById($request->validated()['id']),
            ]);
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
