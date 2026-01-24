<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSubEmail\EditRequest;
use App\Services\ProfileSubEmailService;

class ProfileSubEmailController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfileSubEmailService::class);
    }

    /**
     * Danh sách email phụ của user hiện tại
     */
    public function index()
    {
        return view('admin.pages.user.sub-email.index', [
            'userId' => $this->getUserId(),
        ]);
    }

    /**
     * Form thêm email phụ
     */
    public function create()
    {
        return view('admin.pages.user.sub-email.create', [
            'userId' => $this->getUserId(),
        ]);
    }

    /**
     * Form sửa email phụ
     */
    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            $validated = $request->validated();
            $email = $this->service->findByIdWithOwnershipCheck($validated['id']);

            return view('admin.pages.user.sub-email.edit', [
                'data' => $email,
                'userId' => $this->getUserId(),
            ]);
        });
    }
}
