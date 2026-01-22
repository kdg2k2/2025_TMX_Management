<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\ContractProductMinute;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Repositories\ContractProductMinuteRepository;

class ContractProductMinuteService extends BaseService
{
    public function __construct(
        private ContractMainProductService $contractMainProductService,
        private ContractIntermediateProductService $contractIntermediateProductService,
        private ContractProductInspectionService $contractProductInspectionService,
        private WordService $wordService,
        private HandlerUploadFileService $handlerUploadFileService,
        private UserService $userService,
        private SystemConfigService $systemConfigService,
        private DocumentConversionService $documentConversionService,
        private StringHandlerService $stringHandlerService
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

    public function store(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = parent::store($request);
            return $this->formatRecord($data->toArray());
        }, true);
    }

    protected function afterStore($data, array $request)
    {
        $data->file_docx_path = $this->buildMinute($data);
        $data->save();
    }

    private function buildMinute(ContractProductMinute $data, bool $addSign = false, bool $convertToPdf = false)
    {
        $inspections = $this->contractProductInspectionService->list([
            'contract_id' => $data['contract_id'],
            'status' => 'responded',
        ]);
        if (empty($inspections))
            throw new Exception("Hợp đồng này chưa có yêu cầu kiểm tra nào đã phản hồi!");

        $inspection = $inspections[0];
        $yearFilter = !empty($inspection['years'])
            ? ['years' => collect($inspection['years'])->pluck('year')->toArray()]
            : ['year' => $inspection['contract']['year']];

        $mainProduct = $this->contractMainProductService->list([
            'contract_id' => $data['contract_id'],
            ...$yearFilter,
        ])['data'];
        $intermediateProduct = $this->contractIntermediateProductService->list([
            'contract_id' => $data['contract_id'],
            ...$yearFilter
        ])['data'];

        $data->load([
            'contractProfessional.user:id,name',
            'contractDisbursement.user:id,name',
            ...$this->repository->relations
        ]);

        $pathTemplate =
            $this->handlerUploadFileService->getAbsolutePublicPath(
                $this->getTemplateMinute(count($mainProduct), count($intermediateProduct))
            )
        ;
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
            $filePath = $this->documentConversionService->wordToPdf($this->handlerUploadFileService->getAbsolutePublicPath($filePath));

        return $this->handlerUploadFileService->removePublicPath($filePath);
    }

    private function getTemplateMinute(int $mainCount, int $intermediateCount)
    {
        if ($mainCount + $intermediateCount == 0)
            return 'templates/contract_product_minute_empty.docx';
        if ($mainCount > 0 && $intermediateCount > 0)
            return 'templates/contract_product_minute_both.docx';
        if ($mainCount > 0)
            return 'templates/contract_product_minute_main.docx';
        if ($intermediateCount > 0)
            return 'templates/contract_product_minute_intermediate.docx';
        throw new Exception("Không tìm được template");
    }

    private function fillTemplate(TemplateProcessor $templateProcessor, ContractProductMinute $data, array $inspection, array $mainProduct, array $intermediateProduct, bool $addSign = false)
    {
        $recipientUser = $this->userService->findById($this->systemConfigService->findByKey('CONTRACT_PRODUCT_RECIPIENT_ID', 'key', false)['value'], false);

        $templateProcessor->setValue('cancu', htmlspecialchars($data['legal_basis']) ?? "");
        $templateProcessor->setValue('tenhd', htmlspecialchars($data['contract']['name']) ?? "");
        $templateProcessor->setValue('ngay', htmlspecialchars(date('d', strtotime($data['handover_date']))) ?? "");
        $templateProcessor->setValue('thang', htmlspecialchars(date('m', strtotime($data['handover_date']))) ?? "");
        $templateProcessor->setValue('year', htmlspecialchars(date('Y', strtotime($data['handover_date']))) ?? "");
        $templateProcessor->setValue('tenCty', htmlspecialchars(config('custom.DEFAULT_TITLE') ?? ""));
        $templateProcessor->setValue('ten_chuyenmon', htmlspecialchars($data['contractProfessional']['user']['name']) ?? "");
        $templateProcessor->setValue('ten_giaingan', htmlspecialchars($data['contractDisbursement']['user']['name']) ?? "");
        $templateProcessor->setValue('ten_hoanthien', htmlspecialchars($inspection['contract']['executor_user']['name']) ?? "");
        $templateProcessor->setValue('ten_kiemtra', htmlspecialchars($inspection['inspector_user']['name']) ?? "");
        $templateProcessor->setValue('ten_nhantailieu', htmlspecialchars($recipientUser['name']) ?? "");
        $templateProcessor->setValue('ndbg', htmlspecialchars($data['handover_content']) ?? "");

        $variables = $templateProcessor->getVariables();

        if (count($mainProduct) > 0 && in_array('tensp', $variables)) {
            $templateProcessor->cloneRow('tensp', count($mainProduct));
            foreach ($mainProduct as $index => $value) {
                $templateProcessor->setValue('tt#' . $index + 1, $index + 1);
                $templateProcessor->setValue('nam#' . $index + 1, $value['year'] ?? "");
                $templateProcessor->setValue('tensp#' . $index + 1, htmlspecialchars($value['name']) ?? "");
                $templateProcessor->setValue('soluong#' . $index + 1, (string) $value['quantity'] ?? "");
                $templateProcessor->setValue('ghichu#' . $index + 1, htmlspecialchars($value['note']) ?? "");
            }
        }

        if (count($intermediateProduct) > 0 && in_array('tensp_tg', $variables)) {
            $templateProcessor->cloneRow('tensp_tg', count($intermediateProduct));
            foreach ($intermediateProduct as $index => $value) {
                $templateProcessor->setValue('tt_tg#' . $index + 1, $index + 1);
                $templateProcessor->setValue('hd_tg#' . $index + 1, htmlspecialchars($value['contract_number']) ?? "");
                $templateProcessor->setValue('nguoithuchien#' . $index + 1, $value['executor_user_name'] ?? "");
                $templateProcessor->setValue('tensp_tg#' . $index + 1, htmlspecialchars($value['name']) ?? "");
                $templateProcessor->setValue('gc#' . $index + 1, htmlspecialchars($value['note']) ?? "");
            }
        }

        if ($addSign)
            $this->addSign($templateProcessor, $data, $inspection, $recipientUser);

        $folder = $this->handlerUploadFileService->getAbsolutePublicPath('uploads/render/' . $this->repository->getTable());
        $fileName = time() . '.docx';
        $templateProcessor->saveAs($folder . '/' . $fileName);
        return $folder . '/' . $fileName;
    }

    private function addSign(
        TemplateProcessor $tp,
        ContractProductMinute $data,
        array $inspection,
        User $recipientUser
    ) {
        $set = fn($k, $p) =>
            $p && file_exists($this->handlerUploadFileService->getAbsolutePublicPath($p))
            ? $tp->setImageValue($k, ['path' => $this->handlerUploadFileService->getAbsolutePublicPath($p), 'width' => 55, 'height' => 55])
            : $tp->setValue($k, '');

        $signs = collect($data['signatures'])->keyBy('user_id');

        foreach ([
            'ck_chuyenmon' => $data['contract_professional_id'],
            'ck_giaingan' => $data['contract_disbursement_id'],
            'ck_hoanthien' => $inspection['contract']['executor_user_id'],
            'ck_kiemtra' => $inspection['inspector_user_id'],
            'ck_nhantailieu' => $recipientUser->id,
        ] as $key => $uid) {
            $set($key, $signs[$uid]['signature_path'] ?? null);
        }
    }

}
