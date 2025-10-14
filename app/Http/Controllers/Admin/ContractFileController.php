<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractFile\DeleteRequest;
use App\Services\ContractFileService;

class ContractFileController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractFileService::class);
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
