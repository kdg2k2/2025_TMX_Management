<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonnelFile\DeleteRequest;
use App\Http\Requests\PersonnelFile\EditRequest;
use App\Services\PersonnelFileService;

class PersonnelFileController extends Controller
{
    public function __construct()
    {
        $this->service = app(PersonnelFileService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.file.index', $this->service->baseDataForLCEView());
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.file.create', $this->service->baseDataForLCEView());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.personnels.file.edit', $this->service->baseDataForLCEView($request->validated()['id']));
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
