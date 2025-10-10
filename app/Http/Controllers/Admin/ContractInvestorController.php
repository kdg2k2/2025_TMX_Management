<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractInvestor\DeleteRequest;
use App\Http\Requests\ContractInvestor\EditRequest;
use App\Services\ContractInvestorService;

class ContractInvestorController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractInvestorService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.investor.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.investor.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.contract.investor.edit', [
                'data' => $this->service->findById($request->validated()['id']),
            ]);
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
