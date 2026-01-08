<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfficialDocumentType\DeleteRequest;
use App\Http\Requests\OfficialDocumentType\EditRequest;
use App\Services\OfficialDocumentTypeService;

class OfficialDocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(OfficialDocumentTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.type.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.official-document.type.edit', [
                'data' => $this->service->findById($request->validated()['id'])
            ]);
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
