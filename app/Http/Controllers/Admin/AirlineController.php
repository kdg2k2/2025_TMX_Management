<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Airline\DeleteRequest;
use App\Http\Requests\Airline\EditRequest;
use App\Services\AirlineService;

class AirlineController extends Controller
{
    public function __construct()
    {
        $this->service = app(AirlineService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.airline.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.airline.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.airline.edit', [
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
