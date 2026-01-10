<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KasperskyCode\DeleteRequest;
use App\Http\Requests\KasperskyCode\EditRequest;
use App\Services\KasperskyCodeService;

class KasperskyCodeController extends Controller
{
    public function __construct()
    {
        $this->service = app(KasperskyCodeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.kaspersky.code.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.kaspersky.code.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.kaspersky.code.edit', [
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
