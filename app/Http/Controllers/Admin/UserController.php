<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\EditRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.user.index', $this->service->baseDataForLCEView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.user.create', $this->service->baseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.user.edit', $this->service->baseDataForLCEView($request->validated()['id']));
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
