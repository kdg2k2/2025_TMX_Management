<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractorExperience\DeleteRequest;
use App\Http\Requests\ContractorExperience\EditRequest;
use App\Services\ContractorExperienceService;

class ContractorExperienceController extends Controller
{
    public function __construct()
    {
        $this->service = app(ContractorExperienceService::class);
    }

    public function index()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contractor_experiences.index');
        });
    }

    public function create()
    {
        return $this->catchWeb(function () {
            return view('admin.pages.contractor_experiences.create');
        });
    }

    public function edit(EditRequest $request)
    {
        return $this->catchWeb(function () use ($request) {
            return view('admin.pages.contractor_experiences.edit', [
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
