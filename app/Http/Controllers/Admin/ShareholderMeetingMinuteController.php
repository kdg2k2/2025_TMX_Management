<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShareholderMeetingMinute\DeleteRequest;
use App\Http\Requests\ShareholderMeetingMinute\EditRequest;
use App\Services\ShareholderMeetingMinuteService;

class ShareholderMeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(ShareholderMeetingMinuteService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.shareholder-meeting-minute.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.shareholder-meeting-minute.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.shareholder-meeting-minute.edit', [
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
