<?php

namespace App\Services;

use Exception;
use App\Models\ContractProductMinute;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Repositories\ContractProductMinuteRepository;

class ContractProductMinuteService extends BaseService
{
    private const PENDING_STATUSES = ['request_sign', 'request_approve'];

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
        if (isset($array['file_docx_path']))
            $array['file_docx_path'] = $this->getAssetUrl($array['file_docx_path']);
        if (isset($array['file_pdf_path']))
            $array['file_pdf_path'] = $this->getAssetUrl($array['file_pdf_path']);
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
                'uploads/render/' . $this->repository->getTable(),
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

    private function buildMinute(ContractProductMinute $data, bool $addSign = false, bool $convertToPdf = false)
    {
        $contractId = $data->contract_id;

        $inspections = $this->contractProductInspectionService->list([
            'contract_id' => $contractId,
            'status' => 'responded',
        ]);

        if (empty($inspections))
            throw new Exception("Hợp đồng này chưa có yêu cầu kiểm tra nào đã phản hồi!");

        $inspection = $inspections[0];
        $yearFilter = !empty($inspection['years'])
            ? ['years' => collect($inspection['years'])->pluck('year')->toArray()]
            : ['year' => $inspection['contract']['year']];

        $productFilter = ['contract_id' => $contractId, ...$yearFilter];
        $mainProduct = $this->contractMainProductService->list($productFilter)['data'];
        $intermediateProduct = $this->contractIntermediateProductService->list($productFilter)['data'];

        $data->load($this->repository->relations);

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
            $addSign
        );

        if ($convertToPdf)
            $filePath = $this->documentConversionService->wordToPdf(
                $this->handlerUploadFileService->getAbsolutePublicPath($filePath)
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
        bool $addSign = false
    ): string {
        $recipientId = $this->systemConfigService->findByKey('CONTRACT_PRODUCT_RECIPIENT_ID', 'key', false)['value'];
        $recipientUser = $this->userService->findById($recipientId, false);

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
            'ten_chuyenmon' => $e($data->contractProfessional->user->name ?? ''),
            'ten_giaingan' => $e($data->contractDisbursement->user->name ?? ''),
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

        if ($addSign)
            $this->addSign($tp, $data, $inspection, $recipientUser);

        $folder = $this->handlerUploadFileService->getAbsolutePublicPath(
            'uploads/render/' . $this->repository->getTable()
        );
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
            'ck_chuyenmon' => $data->contract_professional_id,
            'ck_giaingan' => $data->contract_disbursement_id,
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

}
