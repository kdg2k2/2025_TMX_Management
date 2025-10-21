<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SoftwareOwnership\DeleteRequest;
use App\Http\Requests\SoftwareOwnership\EditRequest;
use App\Services\SoftwareOwnershipService;

class SoftwareOwnershipController extends Controller
{
    public function __construct()
    {
        $this->service = app(SoftwareOwnershipService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.software_ownerships.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.software_ownerships.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.software_ownerships.edit', [
                'data' => $this->service->findById($request->validated()['id'])
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
