<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractFileType\DeleteRequest;
use App\Http\Requests\ContractFileType\EditRequest;
use App\Services\ContractFileTypeService;

class ContractFileTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractFileTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.file.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.file.type.create', $this->service->getCreateOrUpdateData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.contract.file.type.edit', $this->service->getCreateOrUpdateData($request->validated()['id']));
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
