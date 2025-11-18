<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dossier\ListRequest;
use App\Http\Requests\DossierMinute\AcceptRequest;
use App\Http\Requests\DossierMinute\DenyRequest;
use App\Services\DossierMinuteService;

class DossierMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierMinuteService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json(
                [
                    'data' => $this->service->listExceptDraftSortByStatus($request->validated()),
                ]
            );
        });
    }

    public function accept(AcceptRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->accept($request->validated());
            return response()->json(
                [
                    'message' => 'Biên bản đã được phê duyệt thành công.',
                ]
            );
        });
    }

    public function deny(DenyRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->deny($request->validated());
            return response()->json(
                [
                    'message' => 'Biên bản đã được từ chối thành công.',
                ]
            );
        });
    }
}
