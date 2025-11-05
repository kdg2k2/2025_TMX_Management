<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dossier\FindByContractIdAndYear;
use App\Http\Requests\Dossier\UploadExcel;
use App\Http\Requests\DossierPlan\ApproveRequest;
use App\Http\Requests\DossierPlan\CreateMinuteRequest;
use App\Services\DossierPlanService;

class DossierPlanController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierPlanService::class);
    }

    public function findByIdContractAndYear(FindByContractIdAndYear $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $data = $this->service->findByIdContractAndYear($validated['contract_id'], $validated['nam']);
            return response()->json($data);
        });
    }

    public function createTempExcel()
    {
        return $this->catchAPI(function () {
            return response()->json([
                'message' => 'Tạo file Excel thành công',
                'data' => $this->service->createTempExcel()
            ]);
        });
    }

    public function uploadExcel(UploadExcel $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $result = $this->service->uploadExcel($validated['contract_id'], $validated['file']);
            return response()->json([
                'message' => 'Upload thành công',
                'data' => $result
            ]);
        });
    }

    public function createMinute(CreateMinuteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $data = $this->service->createMinute($request->validated());

            return response()->json([
                'message' => 'Tạo biên bản thành công',
                'path' => asset($data['path'])
            ]);
        });
    }

    public function sendApproveRequest(ApproveRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->sendApproveRequest($request->validated()['contract_id']);
            return response()->json([
                'message' => 'Yêu cầu duyệt đã được gửi',
            ]);
        });
    }
}
