<?php

namespace App\Services;

use Exception;
use App\Repositories\ContractIntermediateProductRepository;

class ContractIntermediateProductService extends BaseService
{
    public function __construct(
        private ExcelService $excelService,
        private HandlerUploadFileService $handlerUploadFileService
    ) {
        $this->repository = app(ContractIntermediateProductRepository::class);
    }

    public function list(array $request = [])
    {
        return $this->tryThrow(function () use ($request) {
            $data = parent::list($request);

            return [
                'data' => $data,
                'years' => app(ContractProductService::class)->getContractYears($request['contract_id'])
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

            $contract = app(ContractProductService::class)->getContractYears($request['contract_id'], true);
            if (!isset($request['year']))
                $request['year'] = $contract['year'];

            $insertData = array_values(array_map(fn($i) => [
                'contract_id' => $request['contract_id'],
                'year' => $request['year'],
                'name' => $i[1],
                'executor_user_name' => $i[2],
                'note' => $i[3],
            ], $rawData));

            $this->repository->deleteByContractIdAndYear($request['contract_id'], $request['year']);
            $this->repository->insert($insertData);
        }, true);
    }
}
