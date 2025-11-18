<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dossier\UploadExcel;
use App\Services\DossierUsageRegisterService;
use App\Http\Requests\Dossier\FindByContractIdAndYear;
use App\Http\Requests\DossierUsageRegister\SendApproveRequest;

class DossierUsageRegisterController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierUsageRegisterService::class);
    }

    public function findByIdContractAndYear(FindByContractIdAndYear $request)
    {
        return $this->catchAPI(function () use ($request) {
            $validated = $request->validated();
            $data = $this->service->findByIdContractAndYear($validated['contract_id'], $validated['year']);
            return response()->json($data);
        });
    }

    public function createTempExcel(FindByContractIdAndYear $request)
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
            $result = $this->service->uploadExcel($validated['contract_id'], $validated['file']);
            return response()->json([
                'success' => true,
                'message' => 'Upload thành công',
                'data' => $result
            ]);
        });
    }

    public function sendApproveRequest(SendApproveRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $this->service->sendApproveRequest($request->validated());
            return response()->json([
                'message' => 'Yêu cầu duyệt đã được gửi',
            ]);
        });
    }
}
