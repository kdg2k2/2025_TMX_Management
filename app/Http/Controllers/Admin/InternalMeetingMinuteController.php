<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\InternalMeetingMinute\DeleteRequest;
use App\Http\Requests\InternalMeetingMinute\EditRequest;
use App\Services\InternalMeetingMinuteService;

class InternalMeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(InternalMeetingMinuteService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.internal-meeting-minute.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.internal-meeting-minute.create', $this->service->baseDataCreateEdit());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.internal-meeting-minute.edit', $this->service->baseDataCreateEdit($request->validated()['id']));
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
