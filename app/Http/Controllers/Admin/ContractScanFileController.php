<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractScanFile\DeleteRequest;
use App\Services\ContractScanFileService;

class ContractScanFileController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractScanFileService::class);
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
