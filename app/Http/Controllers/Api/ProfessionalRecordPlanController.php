<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionalRecord\FindByContractIdAndYear;
use App\Http\Requests\ProfessionalRecordPlan\ApproveRequest;
use App\Http\Requests\ProfessionalRecordPlan\UploadExcelRequest;
use App\Services\ProfessionalRecordPlanService;

class ProfessionalRecordPlanController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordPlanService::class);
    }

    public function findByIdContractAndYear(FindByContractIdAndYear $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $data = $this->service->findByIdContractAndYear($validated['contract_id'], $validated['year']);
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

    public function uploadExcel(UploadExcelRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $result = $this->service->uploadExcel($request->validated());
            return response()->json([
                'message' => 'Upload thành công',
                'data' => $result
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
