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
                        'name' => 'Số lượng',
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
                $rawData = $this->DossierUsageRegisterService->getAvailable($request['contract_id'], $request['nam'] ?? null);
                $data = $this->prependContractInfo($request['contract_id'], $rawData);
            } else {
                foreach ($request['contract_id'] as $contractId) {
                    $rawData = $this->DossierUsageRegisterService->getAvailable($contractId, $request['nam'] ?? null);
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

            $folder = 'dossier/synthetic';
            $fileName = uniqid('dossier_synthetic') . '.xlsx';
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
            $contract['nam'],
            $contract['tenhd'],
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
