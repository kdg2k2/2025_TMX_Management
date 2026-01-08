<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomingOfficialDocument\DeleteRequest;
use App\Http\Requests\IncomingOfficialDocument\EditRequest;
use App\Services\IncomingOfficialDocumentService;

class IncomingOfficialDocumentController extends Controller
{
    public function __construct()
    {
        $this->service = app(IncomingOfficialDocumentService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.incoming.index', $this->service->getBaseDataForLCEView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.official-document.incoming.create', $this->service->getBaseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.official-document.incoming.edit', $this->service->getBaseDataForLCEView($request->validated()['id']));
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
