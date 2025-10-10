<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contract\DeleteRequest;
use App\Http\Requests\Contract\EditRequest;
use App\Services\ContractService;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.create', $this->service->getCreateOrUpdateData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.contract.edit', $this->service->getCreateOrUpdateData($request->validated()['id']));
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
