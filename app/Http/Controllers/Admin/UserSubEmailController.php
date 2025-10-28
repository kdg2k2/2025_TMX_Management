<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSubEmail\CreateRequest;
use App\Http\Requests\UserSubEmail\EditRequest;
use App\Http\Requests\UserSubEmail\IndexRequest;
use App\Services\UserSubEmailService;

class UserSubEmailController extends Controller
{
    public function __construct()
    {
        $this->service = app(UserSubEmailService::class);
    }

    public function index(IndexRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.user.sub-email.index', [
                'userId' => $request->validated()['user_id'],
            ]);
        });
    }

    public function create(CreateRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.user.sub-email.create', [
                'userId' => $request->validated()['user_id'],
            ]);
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            $validated = $request->validated();
            return view('admin.pages.user.sub-email.edit', [
                'data' => $this->service->findById($validated['id']),
                'userId' => $validated['user_id'],
            ]);
        });
    }
}
