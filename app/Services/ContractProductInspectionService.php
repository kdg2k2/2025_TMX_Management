<?php

namespace App\Services;

use App\Repositories\ContractProductInspectionReporitory;
use Exception;

class ContractProductInspectionService extends BaseService
{
    public function __construct(
        private HandlerUploadFileService $handlerUploadFileService,
        private UserService $userService,
    ) {
        $this->repository = app(ContractProductInspectionReporitory::class);
    }

    public function formatRecord(array $array)
    {
        $array = parent::formatRecord($array);
        if (isset($array['status']))
            $array['status'] = $this->repository->getStatus($array['status']);
        if (isset($array['issue_file_path']))
            $array['issue_file_path'] = $this->getAssetUrl($array['issue_file_path']);
        return $array;
    }

    protected function beforeStore(array $request)
    {
        $contract = app(ContractProductService::class)->findById($request['contract_id'])->toArray();
        $this->isHasProduct($contract);
        $this->checkProductYear($contract, $request['years'] ?? []);

        if (isset($request['issue_file_path']))
            $request['issue_file_path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['issue_file_path'], $this->repository->getTable(), 'issue_file');
        return $request;
    }

    protected function extractBefore(array $request)
    {
        $years = $request['years'] ?? [];
        unset($request['years']);
        return [
            'request' => $request,
            'years' => $years,
        ];
    }

    protected function handleExtractAfter(array $extract, $data)
    {
        $this->syncRelationship($data, 'contract_product_inspection_id', 'years', array_map(fn($i) => ['year' => $i], $extract['years']));
    }

    private function isHasProduct(array $contract)
    {
        if (count($contract['main_products']) + count($contract['intermediate_products']) == 0)
            throw new Exception('Hợp đồng chưa có sản phẩm nào, không thể yêu cầu kiểm tra');
    }

    private function checkProductYear(array $contract, array $years)
    {
        $check = [];
        foreach ($years as $year)
            if (collect($contract['main_products'])->where('year', $year)->count() + collect($contract['intermediate_products'])->where('year', $year)->count() == 0)
                $check[] = $year;

        if (count($check) > 0) {
            $stringYears = implode(', ', $check);
            throw new Exception("Hợp đồng chưa được đẩy sản phẩm năm $stringYears");
        }
    }

    public function cancel(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            $data->update($request);
            return $data;
        });
    }

    public function response(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $data = $this->repository->findById($request['id']);
            if (isset($request['inspector_comment_file_path']))
                $request['inspector_comment_file_path'] = $this->handlerUploadFileService->storeAndRemoveOld($request['inspector_comment_file_path'], $this->repository->getTable(), 'inspector_comment_file');
            $data->update($request);
            return $data;
        });
    }

    private function sendMail(int $id, string $subject, array $files = [])
    {
        $data = $this->findById($id, true, true);
        $emails = $this->getEmails($data);
        dispatch(new \App\Jobs\SendMailJob('emails.contract-product-inspection', $subject . ' kiểm tra sản phẩm', $emails, [
            'data' => $data,
        ], $files));
    }

    private function getEmails($data)
    {
        return $this->userService->getEmails([
            collect([$data['created_by']['id'], $data['supported_by']['id'] ?? null, $data['inspector_user']['id'] ?? null])->unique()->filter()->toArray(),
            app(TaskScheduleService::class)->getUserIdByScheduleKey('CONTRACT_PRODUCT_INSPECTION')
        ]);
    }
}
