<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfessionalRecordSynthetic\CreateFileRequest;
use App\Services\ProfessionalRecordSyntheticService;

class ProfessionalRecordSyntheticController extends Controller
{
    public function __construct()
    {
        $this->service = app(ProfessionalRecordSyntheticService::class);
    }

    public function createSyntheticFile(CreateFileRequest $request)
    {
        return $this->catchAPI(function () use ($request) {
            $path = $this->service->createSyntheticFile($request->validated());
            return response()->json([
                'message' => 'Tạo file excel thành công',
                'path' => $path
            ]);
        });
    }
}
