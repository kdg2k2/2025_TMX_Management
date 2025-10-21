<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonnelUnit\DeleteRequest;
use App\Http\Requests\PersonnelUnit\EditRequest;
use App\Services\PersonnelUnitService;

class PersonnelUnitController extends Controller
{
    public function __construct()
    {
        $this->service = app(PersonnelUnitService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.units.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.personnels.units.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.personnels.units.edit', [
                'data' => $this->service->findById($request->validated()['id'])
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
