<?php

namespace App\Services;

use App\Repositories\ContractProductMinuteSignatureRepository;
use Exception;

class ContractProductMinuteSignatureService extends BaseService
{
    private const UPLOAD_FOLDER = 'signatures/contract-product-minute';

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
        switch ($type) {
            case 'profile':
                // Lấy chữ ký từ profile user
                $user = $this->userService->findById($userId, false);
                if (empty($user['path_signature']))
                    throw new Exception('Bạn chưa có chữ ký cá nhân. Vui lòng cập nhật trong hồ sơ!');
                return $user['path_signature'];

            case 'draw':
                // Cleanup các file rác trước khi tạo file mới
                $this->cleanupOrphanedSignatureFiles();

                // Lưu canvas data thành file
                $base64 = $request['signature_data'];
                // Loại bỏ header data:image/png;base64,
                $base64 = preg_replace('#^data:image/\w+;base64,#i', '', $base64);
                $imageData = base64_decode($base64);

                $fileName = 'signature_' . $userId . '_' . time() . '.png';
                $fullPath = $this->handlerUploadFileService->getAbsolutePublicPath(self::UPLOAD_FOLDER) . '/' . $fileName;

                if (!is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                file_put_contents($fullPath, $imageData);
                return self::UPLOAD_FOLDER . '/' . $fileName;

            case 'upload':
                // Cleanup các file rác trước khi upload file mới
                $this->cleanupOrphanedSignatureFiles();

                // Upload file
                return $this->handlerUploadFileService->storeAndRemoveOld(
                    $request['signature_file'],
                    self::UPLOAD_FOLDER,
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

    /**
     * Xóa các file chữ ký rác trong folder signature không được reference trong DB
     * - File draw/upload cũ khi user ký lại
     * - File không được lưu vào DB (lỗi trong quá trình ký)
     * - Chỉ xóa file cũ hơn 1 giờ để tránh xóa nhầm file đang được tạo
     */
    private function cleanupOrphanedSignatureFiles(): void
    {
        try {
            $folder = $this->handlerUploadFileService->getAbsolutePublicPath(self::UPLOAD_FOLDER);

            if (!is_dir($folder)) {
                return;
            }

            // Lấy tất cả file trong folder
            $allFiles = array_diff(scandir($folder), ['.', '..']);

            // Lấy tất cả path đang được sử dụng trong DB
            $usedPaths = $this->repository->getUsedPaths()
                ->pluck('signature_path')
                ->filter()
                ->map(fn($path) => basename($path))
                ->unique()
                ->toArray();

            $oneHourAgo = time() - 3600;
            $deletedCount = 0;
            $deletedSize = 0;

            foreach ($allFiles as $file) {
                $filePath = $folder . DIRECTORY_SEPARATOR . $file;

                // Chỉ xử lý files
                if (!is_file($filePath)) {
                    continue;
                }

                // Kiểm tra file có được reference trong DB không
                if (in_array($file, $usedPaths, true)) {
                    continue;
                }

                // Chỉ xóa file cũ hơn 1 giờ (tránh xóa nhầm file đang được tạo)
                $fileModifiedTime = filemtime($filePath);
                if ($fileModifiedTime > $oneHourAgo) {
                    continue;
                }

                // Xóa file rác
                $fileSize = filesize($filePath);
                if ($this->handlerUploadFileService->safeDeleteFile($filePath)) {
                    $deletedCount++;
                    $deletedSize += $fileSize;
                    \Log::info("Deleted orphaned signature file: $file (Size: " . round($fileSize / 1024, 2) . ' KB)');
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleanup signature files completed: Deleted $deletedCount orphaned files, freed " . round($deletedSize / 1024 / 1024, 2) . ' MB');
            }
        } catch (Exception $e) {
            // Log error nhưng không throw exception để không ảnh hưởng main process
            \Log::warning('Error cleaning up orphaned signature files: ' . $e->getMessage());
        }
    }
}
