<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionalRecordType\DeleteRequest;
use App\Http\Requests\ProfessionalRecordType\EditRequest;
use App\Http\Requests\ProfessionalRecordType\ImportRequest;
use App\Services\ProfessionalRecordTypeService;

class ProfessionalRecordTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.professional-record.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.professional-record.type.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.professional-record.type.edit', [
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

    public function export()
    {
        return $this->catchAPI(function () {
            return response()->json(
                [
                    'data' => $this->service->export(),
                ]
            );
        });
    }

    public function import(ImportRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->import($request->validated()),
                'message' => config('message.import'),
            ]);
        });
    }
}
