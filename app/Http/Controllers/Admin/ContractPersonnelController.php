<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContractPersonnelService;
use App\Http\Requests\ContractPersonnel\ExportRequest;
use App\Http\Requests\ContractPersonnel\ImportRequest;

class ContractPersonnelController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractPersonnelService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contract.personnel.index', $this->service->baseDataForLView());
        });
    }

    public function export(ExportRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->export($request->validated()),
            'message' => config('message.default'),
        ]));
    }

    public function import(ImportRequest $request)
    {
        return $this->catchAPI(fn() => response()->json([
            'data' => $this->service->import($request->validated()),
            'message' => config('message.default'),
        ]));
    }
}
