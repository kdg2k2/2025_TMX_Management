<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProductMinuteSignature\SignRequest;
use App\Services\ContractProductMinuteSignatureService;

class ContractProductMinuteSignatureController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProductMinuteSignatureService::class);
    }

    public function sign(SignRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            // 'data'=>$this->service->sign($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
