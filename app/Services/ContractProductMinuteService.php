<?php

namespace App\Services;

use App\Models\ContractProductMinute;
use App\Repositories\ContractProductMinuteRepository;
use PhpOffice\PhpWord\TemplateProcessor;
use Exception;

class ContractProductMinuteService extends BaseService
{
    private const PENDING_STATUSES = ['request_sign', 'request_approve'];
    private const UPLOAD_FOLDER = 'uploads/render/contract_product_minutes';

    public function __construct(
        private ContractMainProductService $contractMainProductService,
        private ContractIntermediateProductService $contractIntermediateProductService,
        private ContractProductInspectionService $contractProductInspectionService,
        private WordService $wordService,
        private HandlerUploadFileService $handlerUploadFileService,
        private UserService $userService,
        private SystemConfigService $systemConfigService,
        private DocumentConversionService $documentConversionService,
    ) {
        $this->repository = app(ContractProductMinuteRepository::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['handover_date']))
            $array['handover_date'] = $this->formatDateForPreview($array['handover_date']);
        if (isset($array['file_docx_path']))
            $array['file_docx_path'] = $this->getAssetUrl($array['file_docx_path']);
        if (isset($array['file_pdf_path']))
            $array['file_pdf_path'] = $this->getAssetUrl($array['file_pdf_path']);

        // Format nested signatures using ContractProductMinuteSignatureService
        if (isset($array['signatures']) && is_array($array['signatures'])) {
            $signatureService = app(ContractProductMinuteSignatureService::class);
            $array['signatures'] = $signatureService->formatRecords($array['signatures']);
        }

        return $array;
    }

    public function getStatus($key = null)
    {
        return $this->repository->getStatus($key);
    }

    public function replaceFile(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minute = $this->repository->findById($request['id']);

            if (!in_array($minute->status, ['draft', 'request_sign'], true)) {
                throw new Exception('Chỉ có thể ghi đè file khi biên bản đang ở trạng thái Nháp hoặc Yêu cầu ký!');
            }

            $filePath = $this->handlerUploadFileService->storeAndRemoveOld(
                $request['file_docx'],
                self::UPLOAD_FOLDER,
                null,
                $minute->file_docx_path
            );

            $minute->file_docx_path = $filePath;
            $minute->file_pdf_path = null;
            $minute->save();

            return $this->formatRecord($minute->toArray());
        }, true);
    }

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            // Kiểm tra trạng thái biên bản mới nhất
            $existingMinute = $this->checkLatestMinuteStatus($request['contract_id']);

            if ($existingMinute) {
                // Ghi đè biên bản nháp
                $request['id'] = $existingMinute->id;
                $data = $this->repository->update($request);
            } else {
                // Tạo mới
                $data = parent::store($request);
            }

            return $this->formatRecord($data->toArray());
        }, true);
    }

    protected function afterStore($data, array $request)
    {
        $this->rebuildMinuteFile($data);
    }

    protected function afterUpdate($data, array $request)
    {
        $this->rebuildMinuteFile($data);
    }

    private function rebuildMinuteFile(ContractProductMinute $data)
    {
        $data->file_docx_path = $this->buildMinute($data);
        $data->save();
    }

    /**
     * Kiểm tra trạng thái biên bản mới nhất của hợp đồng
     * - Nếu đang yêu cầu ký hoặc yêu cầu duyệt -> throw exception
     * - Nếu đang là nháp -> trả về biên bản để ghi đè
     * - Nếu không có hoặc đã duyệt/từ chối -> trả về null (tạo mới)
     */
    private function checkLatestMinuteStatus(int $contractId): ?ContractProductMinute
    {
        $latestMinute = $this->repository->findByKey($contractId, 'contract_id', false, false);

        if (!$latestMinute) {
            return null;
        }

        $status = $latestMinute->status;

        if (\in_array($status, self::PENDING_STATUSES, true)) {
            $statusLabel = $this->repository->getStatus($status)['converted'];
            throw new Exception("Hợp đồng đang có biên bản {$statusLabel}, không thể tạo biên bản mới!");
        }

        return $status === 'draft' ? $latestMinute : null;
    }

    private function buildMinute(ContractProductMinute $data, bool $loadFromExisting = false)
    {
        // Cleanup các file rác trước khi tạo file mới
        $this->cleanupOrphanedMinuteFiles();

        $contractId = $data->contract_id;

        // Chuẩn bị dữ liệu inspection và recipientUser (dùng chung cho cả 2 trường hợp)
        [$inspection, $recipientUser] = $this->prepareSignatureData($contractId);
        $data->load($this->repository->relations);

        // Load từ file docx hiện có, fill chữ ký và tự động tạo PDF
        if ($loadFromExisting) {
            if (empty($data->file_docx_path)) {
                throw new Exception('Không tìm thấy file docx của biên bản!');
            }

            $existingFilePath = $this->handlerUploadFileService->getAbsolutePublicPath($data->file_docx_path);
            if (!file_exists($existingFilePath)) {
                throw new Exception('File docx không tồn tại!');
            }

            // Load file hiện có và fill chữ ký
            $tp = $this->wordService->createFromTemplate($existingFilePath);
            $this->addSign($tp, $data, $inspection, $recipientUser);

            // Lưu file docx tạm
            $folder = $this->handlerUploadFileService->getAbsolutePublicPath(self::UPLOAD_FOLDER);
            $fileName = time() . '.docx';
            $savePath = "{$folder}/{$fileName}";
            $tp->saveAs($savePath);

            // Tự động tạo PDF
            $pdfPath = $this->documentConversionService->wordToPdf($savePath);
            return $this->handlerUploadFileService->removePublicPath($pdfPath);
        }

        // Logic tạo mới từ template (không fill chữ ký, chỉ tạo DOCX)
        $yearFilter = !empty($inspection['years'])
            ? ['years' => collect($inspection['years'])->pluck('year')->toArray()]
            : ['year' => $inspection['contract']['year']];

        $productFilter = ['contract_id' => $contractId, ...$yearFilter];
        $mainProduct = $this->contractMainProductService->list($productFilter)['data'];
        $intermediateProduct = $this->contractIntermediateProductService->list($productFilter)['data'];

        $pathTemplate = $this->handlerUploadFileService->getAbsolutePublicPath(
            $this->getTemplateMinute(count($mainProduct), count($intermediateProduct))
        );

        if (!file_exists($pathTemplate))
            throw new Exception("File '$pathTemplate' không tồn tại");

        $filePath = $this->fillTemplate(
            $this->wordService->createFromTemplate($pathTemplate),
            $data,
            $inspection,
            $mainProduct,
            $intermediateProduct,
            $recipientUser
        );

        return $this->handlerUploadFileService->removePublicPath($filePath);
    }

    private function getTemplateMinute(int $mainCount, int $intermediateCount): string
    {
        $hasMain = $mainCount > 0;
        $hasIntermediate = $intermediateCount > 0;

        return match (true) {
            $hasMain && $hasIntermediate => 'templates/contract_product_minute_both.docx',
            $hasMain => 'templates/contract_product_minute_main.docx',
            $hasIntermediate => 'templates/contract_product_minute_intermediate.docx',
            default => 'templates/contract_product_minute_empty.docx',
        };
    }

    private function fillTemplate(
        TemplateProcessor $tp,
        ContractProductMinute $data,
        array $inspection,
        array $mainProduct,
        array $intermediateProduct,
        array $recipientUser
    ): string {
        $handoverDate = strtotime($data->handover_date);
        $e = fn($v) => htmlspecialchars($v ?? '');

        // Set basic values
        $tp->setValues([
            'cancu' => $e($data->legal_basis),
            'tenhd' => $e($data->contract->name ?? ''),
            'ngay' => date('d', $handoverDate),
            'thang' => date('m', $handoverDate),
            'year' => date('Y', $handoverDate),
            'tenCty' => $e(config('custom.DEFAULT_TITLE')),
            'ten_chuyenmon' => $e($data->professionalUser->name ?? ''),
            'ten_giaingan' => $e($data->disbursementUser->name ?? ''),
            'ten_hoanthien' => $e($inspection['contract']['executor_user']['name'] ?? ''),
            'ten_kiemtra' => $e($inspection['inspector_user']['name'] ?? ''),
            'ten_nhantailieu' => $e($recipientUser['name'] ?? ''),
            'ndbg' => $e($data->handover_content),
        ]);

        $variables = $tp->getVariables();

        // Fill main products
        if (\count($mainProduct) > 0 && \in_array('tensp', $variables, true)) {
            $tp->cloneRow('tensp', \count($mainProduct));
            foreach ($mainProduct as $i => $item) {
                $row = $i + 1;
                $tp->setValue("tt#{$row}", $row);
                $tp->setValue("nam#{$row}", $item['year'] ?? '');
                $tp->setValue("tensp#{$row}", $e($item['name']));
                $tp->setValue("soluong#{$row}", (string) ($item['quantity'] ?? ''));
                $tp->setValue("ghichu#{$row}", $e($item['note']));
            }
        }

        // Fill intermediate products
        if (\count($intermediateProduct) > 0 && \in_array('tensp_tg', $variables, true)) {
            $tp->cloneRow('tensp_tg', \count($intermediateProduct));
            foreach ($intermediateProduct as $i => $item) {
                $row = $i + 1;
                $tp->setValue("tt_tg#{$row}", $row);
                $tp->setValue("hd_tg#{$row}", $e($item['contract_number']));
                $tp->setValue("nguoithuchien#{$row}", $item['executor_user_name'] ?? '');
                $tp->setValue("tensp_tg#{$row}", $e($item['name']));
                $tp->setValue("gc#{$row}", $e($item['note']));
            }
        }

        $folder = $this->handlerUploadFileService->getAbsolutePublicPath(self::UPLOAD_FOLDER);
        $fileName = time() . '.docx';
        $tp->saveAs("{$folder}/{$fileName}");

        return "{$folder}/{$fileName}";
    }

    private function addSign(
        TemplateProcessor $tp,
        ContractProductMinute $data,
        array $inspection,
        $recipientUser
    ): void {
        $signs = collect($data->signatures)->keyBy('user_id');
        $imageConfig = ['width' => 55, 'height' => 55];

        $signatureMap = [
            'ck_chuyenmon' => $data->professional_user_id,
            'ck_giaingan' => $data->disbursement_user_id,
            'ck_hoanthien' => $inspection['contract']['executor_user_id'],
            'ck_kiemtra' => $inspection['inspector_user_id'],
            'ck_nhantailieu' => $recipientUser['id'] ?? $recipientUser->id ?? null,
        ];

        foreach ($signatureMap as $key => $userId) {
            $signaturePath = $signs[$userId]['signature_path'] ?? null;

            if ($signaturePath) {
                $absolutePath = $this->handlerUploadFileService->getAbsolutePublicPath($signaturePath);
                if (file_exists($absolutePath)) {
                    $tp->setImageValue($key, ['path' => $absolutePath, ...$imageConfig]);
                    continue;
                }
            }

            $tp->setValue($key, '');
        }
    }

    /**
     * Yêu cầu ký biên bản
     * - Kiểm tra biên bản phải ở trạng thái draft
     * - Tạo các bản ghi signature cho các user cần ký
     * - Cập nhật trạng thái biên bản thành request_sign
     */
    public function signatureRequest(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $minute = $this->repository->findById($request['id']);

            // Kiểm tra trạng thái
            if ($minute->status !== 'draft') {
                throw new Exception('Chỉ có thể yêu cầu ký khi biên bản ở trạng thái Nháp!');
            }

            // Lấy thông tin inspection để xác định các user cần ký
            $inspection = $this->getLatestInspection($minute->contract_id);
            if (!$inspection) {
                throw new Exception('Không tìm thấy thông tin kiểm tra để xác định người ký!');
            }

            $this->syncRelationship($minute, 'contract_product_minute_id', 'signatures', collect($this->getSignatureUserIds($minute, $inspection))->map(fn($i) => ['user_id' => $i])->toArray());

            // Normalize và cập nhật biên bản
            $issueNote = trim($request['issue_note'] ?? '');
            $issueNote = ($issueNote === '') ? null : $issueNote;

            $minute->status = 'request_sign';
            $minute->issue_note = $issueNote;
            $minute->save();

            // Cập nhật trạng thái hợp đồng dựa trên giá trị đã normalize
            // Nếu có ghi chú tồn tại → set status là has_issues, ngược lại là in_progress
            $intermediateProductStatus = ($issueNote !== null)
                ? 'has_issues'
                : 'in_progress';

            $minute->contract()->update([
                'intermediate_product_status' => $intermediateProductStatus,
            ]);
            // Gửi email cho từng người ký
            $this->sendSignatureRequestEmail($minute->id);
            
            return $this->formatRecord($minute->toArray());
        }, true);
    }

    /**
     * Lấy inspection mới nhất đã được phản hồi của hợp đồng
     */
    private function getLatestInspection(int $contractId): ?array
    {
        $inspections = $this->contractProductInspectionService->list([
            'contract_id' => $contractId,
            'status' => 'responded',
        ]);

        return !empty($inspections) ? $inspections[0] : null;
    }

    /**
     * Lấy recipient user từ system config
     */
    private function getRecipientUser()
    {
        $recipientId = $this->systemConfigService->findByKey('CONTRACT_PRODUCT_RECIPIENT_ID', 'key', false)['value'];
        return $this->userService->findById($recipientId, false, true);
    }

    /**
     * Lấy danh sách user IDs cần ký biên bản
     */
    private function getSignatureUserIds(ContractProductMinute $minute, array $inspection)
    {
        $recipientId = $this->systemConfigService->findByKey('CONTRACT_PRODUCT_RECIPIENT_ID', 'key', false)['value'];

        return array_unique(array_filter([
            $minute->professional_user_id,  // Phụ trách chuyên môn
            $minute->disbursement_user_id,  // Phụ trách giải ngân
            $inspection['contract']['executor_user_id'],  // Người hoàn thiện
            $inspection['inspector_user_id'],  // Người kiểm tra
            $recipientId,  // Người nhận tài liệu
        ]));
    }

    /**
     * Chuẩn bị dữ liệu cho việc fill chữ ký
     * Trả về [inspection, recipientUser] hoặc throw exception
     */
    private function prepareSignatureData(int $contractId)
    {
        $inspection = $this->getLatestInspection($contractId);
        if (!$inspection) {
            throw new Exception('Không tìm thấy thông tin kiểm tra để xác định người ký!');
        }

        $recipientUser = $this->getRecipientUser();

        return [$inspection, $recipientUser];
    }

    /**
     * Xóa các file rác trong folder minute không được reference trong DB
     * - File docx tạm khi convert sang PDF
     * - File cũ khi rebuild minute
     * - Chỉ xóa file cũ hơn 1 giờ để tránh xóa nhầm file đang được tạo
     */
    private function cleanupOrphanedMinuteFiles(): void
    {
        try {
            $folder = $this->handlerUploadFileService->getAbsolutePublicPath(self::UPLOAD_FOLDER);

            if (!is_dir($folder)) {
                return;
            }

            // Lấy tất cả file trong folder
            $allFiles = array_diff(scandir($folder), ['.', '..']);

            // Lấy tất cả path đang được sử dụng trong DB
            $usedPaths = $this
                ->repository
                ->getUsedPaths()
                ->flatMap(fn($m) => [$m->file_docx_path, $m->file_pdf_path])
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
                    \Log::info("Deleted orphaned minute file: $file (Size: " . round($fileSize / 1024, 2) . ' KB)');
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleanup minute files completed: Deleted $deletedCount orphaned files, freed " . round($deletedSize / 1024 / 1024, 2) . ' MB');
            }
        } catch (Exception $e) {
            // Log error nhưng không throw exception để không ảnh hưởng main process
            \Log::warning('Error cleaning up orphaned minute files: ' . $e->getMessage());
        }
    }

    /**
     * Gửi email yêu cầu ký cho các user
     */
    private function sendSignatureRequestEmail(int $minuteId): void
    {
        $baseEmailData = $this->getBaseEmailData($minuteId);

        // Lấy danh sách email của các user cần ký
        $userIds = collect($baseEmailData['minute']['signatures'])->pluck('user_id')->unique()->filter()->toArray();
        $emails = $this->userService->getEmails([$userIds]);

        if (empty($emails)) {
            return;
        }

        $docPath = $baseEmailData['minute']['file_docx_path']
            ? $this->handlerUploadFileService->getAbsolutePublicPath($this->removeAssetUrl($baseEmailData['minute']['file_docx_path']))
            : null;

        // Gửi email
        dispatch(new \App\Jobs\SendMailJob(
            'emails.contract-product-minute-signature',
            'Yêu cầu ký biên bản sản phẩm hợp đồng',
            $emails,
            array_merge(
                $baseEmailData,
                [
                    'signUrl' => route('contract.product.minute.sign.index', ['minute_id' => $minuteId]),
                ]
            ),
            $docPath && file_exists($docPath) ? [$docPath] : []
        ));
    }

    /**
     * Fill chữ ký vào file docx, tạo PDF và gửi email yêu cầu duyệt
     * Được gọi khi tất cả người ký đã hoàn tất ký
     */
    public function fillSignatureAndCreatePdf(int $minuteId): void
    {
        $minute = $this->repository->findById($minuteId);

        // 1. Fill chữ ký vào file docx và tạo PDF (loadFromExisting=true tự động fill chữ ký và tạo PDF)
        $pdfPath = $this->buildMinute($minute, true);

        // 2. Lưu path PDF và cập nhật status
        $minute->file_pdf_path = $pdfPath;
        $minute->status = 'request_approve';
        $minute->save();

        // 3. Gửi email yêu cầu duyệt
        $this->sendApprovalRequestEmail($minuteId);
    }

    private function getBaseEmailData(int $minuteId)
    {
        $minute = $this->findById($minuteId, true, true);
        $inspection = $this->getLatestInspection($minute['contract_id']);
        return [
            'minute' => $minute,
            'inspection' => $inspection,
        ];
    }

    /**
     * Gửi email yêu cầu duyệt biên bản
     * - Gửi cho tất cả người đã ký
     * - Gửi cho toàn bộ thành viên của hợp đồng
     * - Đính kèm file PDF
     */
    public function sendApprovalRequestEmail(int $minuteId): void
    {
        $baseEmailData = $this->getBaseEmailData($minuteId);

        // Lấy email của các người đã ký
        $signerUserIds = collect($baseEmailData['minute']['signatures'])->pluck('user_id')->unique()->filter()->toArray();

        // Lấy email của toàn bộ thành viên hợp đồng
        $contractId = $baseEmailData['minute']['contract']['id'] ?? $baseEmailData['minute']['contract_id'];
        $contractService = app(ContractService::class);
        $memberEmails = $contractService->getMemberEmails($contractId, [
            'accounting_contact',
            'inspector_user',
            'executor_user',
            'instructors',
            'professionals',
            'disbursements',
            'intermediate_collaborators',
        ]);

        // Lấy email của người ký
        $signerEmails = $this->userService->getEmails([$signerUserIds]);

        // Gộp tất cả email và loại bỏ duplicate
        $allEmails = array_unique(array_merge($signerEmails, $memberEmails));

        if (empty($allEmails)) {
            return;
        }

        // Lấy path file PDF để đính kèm
        $pdfPath = $baseEmailData['minute']['file_pdf_path']
            ? $this->handlerUploadFileService->getAbsolutePublicPath($this->removeAssetUrl($baseEmailData['minute']['file_pdf_path']))
            : null;

        // Gửi email với file PDF đính kèm
        dispatch(new \App\Jobs\SendMailJob(
            'emails.contract-product-minute-base-content',
            'Yêu cầu duyệt biên bản sản phẩm hợp đồng',
            $allEmails,
            $baseEmailData,
            $pdfPath && file_exists($pdfPath) ? [$pdfPath] : []
        ));
    }
}
