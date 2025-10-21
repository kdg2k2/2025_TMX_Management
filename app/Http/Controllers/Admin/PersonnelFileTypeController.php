<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonnelFileType\DeleteRequest;
use App\Http\Requests\PersonnelFileType\EditRequest;
use App\Services\PersonnelFileTypeService;

class PersonnelFileTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(PersonnelFileTypeService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.file.type.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.file.type.create', $this->service->getCreateOrUpdateBaseData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.personnels.file.type.edit', $this->service->getCreateOrUpdateBaseData($request->validated()['id']));
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
