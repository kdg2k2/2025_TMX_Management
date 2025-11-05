<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoardMeetingMinute\DeleteRequest;
use App\Http\Requests\BoardMeetingMinute\EditRequest;
use App\Services\BoardMeetingMinuteService;

class BoardMeetingMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(BoardMeetingMinuteService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.board-meeting-minute.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.board-meeting-minute.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.board-meeting-minute.edit', [
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
