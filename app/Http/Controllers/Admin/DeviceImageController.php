<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceImage\DeleteRequest;
use App\Services\DeviceImageService;

class DeviceImageController extends Controller
{
    public function __construct()
    {
        $this->service = app(DeviceImageService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.device.image.index');
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
