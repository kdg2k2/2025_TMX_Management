<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DossierSynthetic\CreateFileRequest;
use App\Services\DossierSyntheticService;

class DossierSyntheticController extends Controller
{
    public function __construct()
    {
        $this->service = app(DossierSyntheticService::class);
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
