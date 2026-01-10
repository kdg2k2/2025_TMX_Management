<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficialDocument\ApproveRequest;
use App\Http\Requests\OfficialDocument\DeleteRequest;
use App\Http\Requests\OfficialDocument\EditRequest;
use App\Http\Requests\OfficialDocument\RejectRequest;
use App\Http\Requests\OfficialDocument\ReleaseRequest;
use App\Http\Requests\OfficialDocument\ReviewApproveRequest;
use App\Http\Requests\OfficialDocument\ReviewRejectRequest;
use App\Services\OfficialDocumentService;

class OfficialDocumentController extends Controller
{
    public function __construct()
    {
        $this->service = app(OfficialDocumentService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.index', $this->service->getBaseDataForLCEView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.create', $this->service->getBaseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.official-document.edit', $this->service->getBaseDataForLCEView($request->validated()['id']));
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

    public function reviewApprove(ReviewApproveRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->reviewApprove($request->validated()),
                'message' => config('message.default'),
            ]);
        });
    }

    public function reviewReject(ReviewRejectRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->reviewReject($request->validated()),
                'message' => config('message.default'),
            ]);
        });
    }

    public function approve(ApproveRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->approve($request->validated()),
                'message' => config('message.approve'),
            ]);
        });
    }

    public function reject(RejectRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->reject($request->validated()),
                'message' => config('message.reject'),
            ]);
        });
    }

    public function release(ReleaseRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->release($request->validated()),
                'message' => config('message.default'),
            ]);
        });
    }
}
