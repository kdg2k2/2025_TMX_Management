<?php
namespace App\Services;

class ProfessionalRecordService extends BaseService
{
    public function __construct(
        private ContractService $contractService
    ) {}

    public function baseIndexData()
    {
        return $this->tryThrow(function () {
            return [
                'contracts' => $this->contractService->list([
                    'load_relations' => false,
                ]),
                'years' => $this->contractService->getYears(),
            ];
        });
    }

    public function baseCreateTempExcel()
    {
        return $this->tryThrow(function () {
            {
                $types = array_map(function ($item) {
                    return [
                        $item['name'],
                        $item['unit'],
                    ];
                }, app(ProfessionalRecordTypeService::class)->list());

                $communes = array_map(function ($item) {
                    return [
                        $item['province']['name'],
                        $item['name'],
                    ];
                }, app(CommuneService::class)->list());

                $units = array_map(function ($item) {
                    return [
                        $item['province']['name'],
                        $item['name'],
                    ];
                }, app(UnitService::class)->list());

                $users = array_map(fn($i) => [
                    $i['name'],
                    $i['email'],
                ], app(UserService::class)->list([
                    'load_relations' => false,
                    'columns' => [
                        'name',
                        'email',
                    ],
                    'is_banned' => false,
                    'is_retired' => false,
                ]));

                return [
                    (object) [
                        'name' => 'Loại biên bản',
                        'header' => [
                            [
                                [
                                    'name' => 'Tên',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                                [
                                    'name' => 'Đơn vị tính',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                            ]
                        ],
                        'data' => $types,
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                    (object) [
                        'name' => 'Tỉnh - xã',
                        'header' => [
                            [
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
                            ]
                        ],
                        'data' => $communes,
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                    (object) [
                        'name' => 'Đơn vị',
                        'header' => [
                            [
                                [
                                    'name' => 'Tỉnh',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                                [
                                    'name' => 'Tên đơn vị',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                            ]
                        ],
                        'data' => $units,
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                    (object) [
                        'name' => 'Nhân sự',
                        'header' => [
                            [
                                [
                                    'name' => 'Tên',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                                [
                                    'name' => 'Email',
                                    'rowspan' => 1,
                                    'colspan' => 1,
                                ],
                            ]
                        ],
                        'data' => $users,
                        'boldRows' => [1],
                        'boldItalicRows' => [],
                        'italicRows' => [],
                        'centerColumns' => [],
                        'centerRows' => [],
                        'filterStartRow' => 1,
                        'freezePane' => 'freezeTopRow',
                    ],
                ];
            }
        });
    }

    public function createExcel(string $folder, string $fileName, array $sheet)
    {
        return app(ExcelService::class)->createExcel(
            $sheet,
            $folder,
            $fileName,
            false,
            35
        );
    }
}
