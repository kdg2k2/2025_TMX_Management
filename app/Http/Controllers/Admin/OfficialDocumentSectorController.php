<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OfficialDocumentSectorService;
use App\Http\Requests\OfficialDocumentSector\EditRequest;
use App\Http\Requests\OfficialDocumentSector\DeleteRequest;

class OfficialDocumentSectorController extends Controller
{
    public function __construct()
    {
        $this->service = app(OfficialDocumentSectorService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.sector.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.sector.create', $this->service->getBaseDataForCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.official-document.sector.edit', $this->service->getBaseDataForCEView($request->validated()['id']));
        });
    }

    public function delete(DeleteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->delete($request->validated()['id']);
            return response()->json([
                'message' => config('message.delete'),
            ]);
        });
    }
}
