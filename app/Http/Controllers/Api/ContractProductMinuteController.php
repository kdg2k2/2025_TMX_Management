<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProductMinute\ConfirmIssuesRequest;
use App\Http\Requests\ContractProductMinute\CreateMinuteRequest;
use App\Http\Requests\ContractProductMinute\ListRequest;
use App\Http\Requests\ContractProductMinute\ReplaceMinuteRequest;
use App\Http\Requests\ContractProductMinute\SignatureRequest;
use App\Services\ContractProductMinuteService;

class ContractProductMinuteController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductMinuteService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }

    public function create(CreateMinuteRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->store($request->validated()),
            'message' => config('message.default'),
        ]));
    }

    public function replace(ReplaceMinuteRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->replaceFile($request->validated()),
            'message' => config('message.default'),
        ]));
    }

    public function signatureRequest(SignatureRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->signatureRequest($request->validated()),
            'message' => config('message.default'),
        ]));
    }

    public function confirmIssues(ConfirmIssuesRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->confirmIssues($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
