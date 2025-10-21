<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PersonnelFileTypeService;
use App\Http\Requests\PersonnelFileType\ListRequest;
use App\Http\Requests\PersonnelFileType\StoreRequest;
use App\Http\Requests\PersonnelFileType\UpdateRequest;

class PersonnelFileTypeController extends Controller
{
    public function __construct()
    {
        $this->service = app(PersonnelFileTypeService::class);
    }

    public function list(ListRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->list($request->validated()),
            ]);
        });
    }

    public function store(StoreRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->store($request->validated()),
                'message' => config('message.store'),
            ]);
        });
    }

    public function update(UpdateRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            return response()->json([
                'data' => $this->service->update($request->validated()),
                'message' => config('message.update'),
            ]);
        });
    }
}
