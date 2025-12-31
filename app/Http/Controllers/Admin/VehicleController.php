<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vehicle\DeleteRequest;
use App\Http\Requests\Vehicle\EditRequest;
use App\Services\VehicleService;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->service = app(VehicleService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.vehicle.index', $this->service->baseDataForLCEView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.vehicle.create', $this->service->baseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.vehicle.edit', $this->service->baseDataForLCEView($request->validated()['id']));
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
