<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentContractPersonnel\DeleteRequest;
use App\Http\Requests\EmploymentContractPersonnel\EditRequest;
use App\Services\EmploymentContractPersonnelService;

class EmploymentContractPersonnelController extends Controller
{
    public function __construct()
    {
        $this->service = app(EmploymentContractPersonnelService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.employment-contract-personnel.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.employment-contract-personnel.create', $this->service->getCreateOrUpdateBaseData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.employment-contract-personnel.edit', $this->service->getCreateOrUpdateBaseData($request->validated()['id']));
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
