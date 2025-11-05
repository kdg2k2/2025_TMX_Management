<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\DeleteRequest;
use App\Http\Requests\Unit\EditRequest;
use App\Services\UnitService;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->service = app(UnitService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.unit.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.unit.create', $this->service->getNeededData());
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.unit.edit', $this->service->getNeededData($request->validated()['id']));
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
