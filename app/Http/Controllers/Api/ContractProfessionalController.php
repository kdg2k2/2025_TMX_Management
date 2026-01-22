<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractProfessional\ListRequest;
use App\Services\ContractProfessionalService;

class ContractProfessionalController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractProfessionalService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->list($request->validated()),
        ]));
    }
}
