<?php

namespace App\Services;

use App\Repositories\ContractProductMinuteSignatureRepository;
use Exception;

class ContractProductMinuteSignatureService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private UserService $userService,
        private ContractProductMinuteService $contractProductMinuteService,
    ) {
        $this->repository = app(ContractProductMinuteSignatureRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['type']))
            $array['type'] = $this->repository->getType($array['type']);
        if (isset($array['signature_path']))
            $array['signature_path'] = $this->getAssetUrl($array['signature_path']);
        if (isset($array['signed_at']))
            $array['signed_at'] = $this->formatDateTimeForPreview($array['signed_at']);
        return $array;
    }

    /**
     * Ký biên bản
     * - Kiểm tra user có quyền ký không
     * - Lưu chữ ký (từ profile/draw/upload)
     * - Cập nhật trạng thái signature thành signed
     * - Kiểm tra nếu tất cả đã ký → chuyển minute sang request_approve
     */
    public function sign(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minuteId = $request['minute_id'];
            $userId = $this->getUserId();
            $signatureType = $request['signature_type'];

            // Tìm signature của user
            $signature = $this->repository->getMinuteSignByUserId($userId, $minuteId);

            if (!$signature)
                throw new Exception('Bạn không có quyền ký biên bản này!');

            if ($signature->status === 'signed')
                throw new Exception('Bạn đã ký biên bản này rồi!');

            // Xử lý lưu chữ ký theo type
            $signaturePath = $this->handleSignature($signatureType, $request, $userId);

            // Cập nhật signature
            $signature->update([
                'type' => $signatureType,
                'signature_path' => $signaturePath,
                'status' => 'signed',
                'signed_at' => now(),
            ]);

            // Kiểm tra xem tất cả đã ký chưa
            $this->checkAndUpdateMinuteStatus($minuteId);

            return $this->formatRecord($signature->toArray());
        }, true);
    }

    /**
     * Xử lý lưu chữ ký theo từng type
     */
    private function handleSignature(string $type, array $request, int $userId): string
    {
        $folder = 'uploads/signatures/contract-product-minute';

        switch ($type) {
            case 'profile':
                // Lấy chữ ký từ profile user
                $user = $this->userService->findById($userId, false);
                if (empty($user['path_signature']))
                    throw new Exception('Bạn chưa có chữ ký cá nhân. Vui lòng cập nhật trong hồ sơ!');
                return $user['path_signature'];

            case 'draw':
                // Lưu canvas data thành file
                $base64 = $request['signature_data'];
                // Loại bỏ header data:image/png;base64,
                $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
                $imageData = base64_decode($base64);

                $fileName = 'signature_' . $userId . '_' . time() . '.png';
                $fullPath = $this->handlerUploadFileService->getAbsolutePublicPath($folder) . '/' . $fileName;

                if (!is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                file_put_contents($fullPath, $imageData);
                return $folder . '/' . $fileName;

            case 'upload':
                // Upload file
                return $this->handlerUploadFileService->storeAndRemoveOld(
                    $request['signature_file'],
                    $folder,
                );

            default:
                throw new Exception('Phương thức ký không hợp lệ');
        }
    }

    /**
     * Kiểm tra nếu tất cả đã ký → fill chữ ký, tạo PDF, gửi email và chuyển minute sang request_approve
     */
    private function checkAndUpdateMinuteStatus(int $minuteId): void
    {
        $allSigned = $this->repository->isMinuteSigned($minuteId);
        if ($allSigned)
            $this->contractProductMinuteService->fillSignatureAndCreatePdf($minuteId);
    }
}
