<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DossierType\DeleteRequest;
use App\Http\Requests\DossierType\EditRequest;
use App\Http\Requests\DossierType\ImportRequest;
use App\Services\DossierTypeService;

class DossierTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.dossier.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.dossier.type.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.dossier.type.edit', [
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
