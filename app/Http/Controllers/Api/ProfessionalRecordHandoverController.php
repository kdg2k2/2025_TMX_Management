<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfessionalRecordHandoverService;
use App\Http\Requests\ProfessionalRecord\UploadExcel;
use App\Http\Requests\ProfessionalRecord\CreateMinuteRequest;
use App\Http\Requests\ProfessionalRecord\FindByContractIdAndYear;

class ProfessionalRecordHandoverController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordHandoverService::class);
    }

    public function findByIdContractAndYear(FindByContractIdAndYear $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $data = $this->service->findByIdContractAndYear($validated['contract_id'], $validated['year']);
            return response()->json($data);
        });
    }

    public function createTempExcel(CreateMinuteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $data = $this->service->createTempExcel($request->validated());
            return response()->json([
                'message' => 'Tạo file Excel thành công',
                'data' => $data
            ]);
        });
    }

    public function uploadExcel(UploadExcel $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $this->service->uploadExcel($validated['contract_id'], $validated['file']);
            return response()->json([
                'success' => true,
                'message' => 'Upload thành công',
            ]);
        });
    }

    public function createMinute(CreateMinuteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $data = $this->service->createMinute($request->validated()['contract_id']);
            return response()->json([
                'message' => 'Tạo biên bản thành công',
                'path' => asset($data['path'])
            ]);
        });
    }

    public function sendApproveRequest(CreateMinuteRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->sendApproveRequest($request->validated()['contract_id']);
            return response()->json([
                'message' => 'Yêu cầu duyệt đã được gửi',
            ]);
        });
    }
}
