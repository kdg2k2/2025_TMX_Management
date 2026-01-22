<?php

namespace App\Services;

use App\Repositories\ContractIntermediateProductRepository;
use Exception;

class ContractIntermediateProductService extends BaseService
{
    public function __construct(
        private ExcelService $excelService,
        private HandlerUploadFileService $handlerUploadFileService,
        private ContractManyYearService $contractManyYearService
    ) {
        $this->repository = app(ContractIntermediateProductRepository::class);
    }

    public function list(array $request = [])
    {
        return $this->tryThrow(function () use ($request) {
            $data = parent::list($request);

            return [
                'data' => $data,
                'years' => $this->contractManyYearService->list([
                    'contract_id' => $request['contract_id'],
                ])
            ];
        });
    }

    public function export(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $folder = 'uploads/render/product/intermediate';
            $this->handlerUploadFileService->cleanupOldOverlapFiles($folder);
            return asset($this->excelService->createExcel(
                [
                    (object) [
                        'name' => 'data',
                        'header' => $this->getHeaderExport(),
                        'data' => $this->getDataExport($request),
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ]
                ],
                $folder,
                'intermediate_product_' . date('d-m-Y_H-i-s') . '.xlsx'
            ));
        });
    }

    private function getDataExport(array $request)
    {
        $data = $this->list([
            'contract_id' => $request['contract_id'],
            'columns' => [
                'year',
                'contract_number',
                'name',
                'executor_user_name',
                'note',
            ]
        ]);
        return collect($data['data'])->values()->map(fn($i) => array_values($i))->toArray();
    }

    private function getHeaderExport()
    {
        return [
            [
                ...array_map(fn($i) => [
                    'name' => $i,
                    'row_span' => 1,
                    'col_span' => 1,
                ], [
                    'Năm',
                    'Số hợp đồng',
                    'Tên sản phẩm',
                    'Tên người thực hiện',
                    'Ghi chú',
                ])
            ]
        ];
    }

    public function import(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $excelData = $this->excelService->readExcel($request['file']);
            $rawData = $excelData['data'] ?? [];
            if (count($rawData) <= 1)
                throw new Exception("Không tìm thấy dữ liệu trong sheet 'data'!");
            unset($rawData[0]);

            $contract = app(ContractProductService::class)->findById($request['contract_id']);
            if (!isset($request['year']))
                $request['year'] = $contract['year'];

            $insertData = array_values(array_map(fn($i) => [
                'contract_id' => $request['contract_id'],
                'year' => $request['year'],
                'contract_number' => $i[1],
                'name' => $i[2],
                'executor_user_name' => $i[3],
                'note' => $i[4],
            ], $rawData));

            $this->repository->deleteByContractIdAndYear($request['contract_id'], $request['year']);
            $this->repository->insert($insertData);
        }, true);
    }
}
