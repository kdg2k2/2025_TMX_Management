<?php
namespace App\Services;

class DossierSyntheticService extends BaseService
{
    protected $DossierUsageRegisterService;
    protected $ContractService;

    public function __construct()
    {
        $this->DossierUsageRegisterService = app(DossierUsageRegisterService::class);
        $this->ContractService = app(ContractService::class);
    }

    public function baseIndexData()
    {
        return $this->tryThrow(function () {
            return app(DossierService::class)->baseIndexData();
        });
    }

    public function createSyntheticFile(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            if (empty($request['contract_id']))
                $request['contract_id'] = $this->ContractService->getIds();

            $header =
                [
                    [
                        'name' => 'Năm',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Hợp đồng',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Loại biên bản',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Tỉnh',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Xã',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Đơn vị',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Kế hoạch',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Bàn giao',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Sử dụng',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Khả dụng',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Đơn vị tính',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                    [
                        'name' => 'Ghi chú',
                        'rowspan' => 1,
                        'colspan' => 1,
                    ],
                ];

            $data = [];
            if (!is_array($request['contract_id'])) {
                $rawData = $this->DossierUsageRegisterService->getAvailable($request['contract_id'], $request['year'] ?? null, true);
                $data = $this->prependContractInfo($request['contract_id'], $rawData);
            } else {
                foreach ($request['contract_id'] as $contractId) {
                    $rawData = $this->DossierUsageRegisterService->getAvailable($contractId, $request['year'] ?? null, true);
                    if (empty($rawData))
                        continue;
                    $data = array_merge($data, $this->prependContractInfo($contractId, $rawData));
                }
            }

            $data = array_values($data);

            $sheets[] = (object) [
                'name' => 'data',
                'header' => [$header],
                'data' => $data,
                'boldRows' => [1],
                'boldItalicRows' => [],
                'italicRows' => [],
                'centerColumns' => [],
                'centerRows' => [],
                'filterStartRow' => 1,
                'freezePane' => 'freezeTopRow',
            ];

            $folder = 'uploads/dossier/synthetic';
            $fileName = 'dossier_synthetic_' . date('d-m-Y_H-i-s') . '.xlsx';
            return asset(
                app(ExcelService::class)->createExcel(
                    $sheets,
                    $folder,
                    $fileName
                )
            );
        });
    }

    private function prependContractInfo(int $contractId, array $data)
    {
        $contract = $this->ContractService->findById($contractId);

        $contractInfo = [
            $contract['year'],
            $contract['name'],
        ];

        foreach ($data as $key => &$item) {
            if (is_array($item)) {
                $item = [...$contractInfo, ...$item];
            }
        }
        unset($item);

        return $data;
    }
}
