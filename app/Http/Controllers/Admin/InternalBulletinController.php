<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InternalBulletin\DeleteRequest;
use App\Http\Requests\InternalBulletin\EditRequest;
use App\Services\InternalBulletinService;

class InternalBulletinController extends Controller
{
    public function __construct()
    {
        $this->service = app(InternalBulletinService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.internal-bulletin.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.internal-bulletin.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.internal-bulletin.edit', [
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
