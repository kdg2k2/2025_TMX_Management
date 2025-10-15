<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractAppendix\DeleteRequest;
use App\Services\ContractAppendixService;

class ContractAppendixController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractAppendixService::class);
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
