<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonnelCustomField\DeleteRequest;
use App\Http\Requests\PersonnelCustomField\EditRequest;
use App\Services\PersonnelCustomFieldService;

class PersonnelCustomFieldController extends Controller
{
    public function __construct()
    {
        $this->service = app(PersonnelCustomFieldService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.custom-field.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.custom-field.create', $this->service->getCreateOrUpdateBaseData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.personnels.custom-field.edit', $this->service->getCreateOrUpdateBaseData($request->validated()['id']));
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
