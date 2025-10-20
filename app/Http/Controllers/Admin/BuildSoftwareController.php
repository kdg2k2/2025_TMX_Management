<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuildSoftware\AcceptRequest;
use App\Http\Requests\BuildSoftware\DeleteRequest;
use App\Http\Requests\BuildSoftware\EditRequest;
use App\Http\Requests\BuildSoftware\RejectRequest;
use App\Http\Requests\BuildSoftware\UpdateStateRequest;
use App\Services\BuildSoftwareService;

class BuildSoftwareController extends Controller
{
    public function __construct()
    {
        $this->service = app(BuildSoftwareService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.build-software.index', $this->service->getListBaseData());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.build-software.create', $this->service->getCreateOrUpdateBaseData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.build-software.edit', $this->service->getCreateOrUpdateBaseData($request->validated()['id']));
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

    public function accept(AcceptRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->accept($request->validated());
            return response()->json([
                'message' => config('message.accept'),
            ]);
        });
    }

    public function reject(RejectRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->reject($request->validated());
            return response()->json([
                'message' => config('message.reject'),
            ]);
        });
    }

    public function updateState(UpdateStateRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->updateState($request->validated());
            return response()->json([
                'message' => config('message.update'),
            ]);
        });
    }
}
