<?php

namespace App\Services;

use App\Repositories\DossierTypeRepository;

class DossierTypeService extends BaseService
{
    public function __construct()
    {
        $this->repository = app(DossierTypeRepository::class);
    }

    public function beforeImport(array $request)
    {
        return array_map([$this, 'beforeStore'], $request);
    }

    public function export()
    {
        return $this->tryThrow(function () {
            $data = array_map(function ($item) {
                return [
                    $item['id'],
                    $item['name'],
                    $item['unit'],
                    $item['quantity'],
                    $item['quantity_limit'],
                ];
            }, $this->repository->list());

            $sheets = [
                (object) [
                    'name' => 'data',
                    'header' => [
                        [
                            [
                                'name' => 'ID (KHÔNG SỬA CỘT NÀY)',
                                'colspan' => 1,
                                'rowspan' => 1,
                            ],
                            [
                                'name' => 'Loại hồ sơ',
                                'colspan' => 1,
                                'rowspan' => 1,
                            ],
                            [
                                'name' => 'Đơn vị tính',
                                'colspan' => 1,
                                'rowspan' => 1,
                            ],
                            [
                                'name' => 'Số lượng khả dụng',
                                'colspan' => 1,
                                'rowspan' => 1,
                            ],
                            [
                                'name' => 'Số lượng khả dụng tối thiểu',
                                'colspan' => 1,
                                'rowspan' => 1,
                            ],
                        ]
                    ],
                    'data' => $data,
                    'boldRows' => [1],
                    'boldItalicRows' => [],
                    'italicRows' => [],
                    'centerColumns' => [],
                    'centerRows' => [],
                    'filterStartRow' => 1,
                    'freezePane' => 'freezeTopRow',
                ]
            ];

            return asset(app(DossierService::class)->createExcel(
                'uploads/dossier/type',
                uniqid('dossier_type') . '.xlsx',
                $sheets
            ));
        });
    }

    public function import(array $request)
    {
        return $this->tryThrow(function () use ($request) {
            $file = $request['file'];
            $sheetsData = app(ExcelService::class)->readExcel($file);
            $data = $sheetsData['data'] ?? [];
            if (empty($data))
                throw new \Exception('File không có sheet data! Vui lòng giữ nguyên cấu trúc file ban đầu!');

            unset($data[0]);

            $errors = [];
            foreach ($data as &$item) {
                $item[3] = (int) $item[3];
                $item[4] = (int) $item[4];
                if ($item[3] != 0 || $item[4] != 0) {
                    // Kiểm tra null hoặc chuỗi rỗng, không dùng empty()
                    if (is_null($item[3]) || $item[3] === '' || is_null($item[4]) || $item[4] === '')
                        $errors[] = "'{$item[1]}' - số lượng khả dụng và số lượng khả dụng tối thiểu không được để trống";

                    if ($item[3] < 0 || $item[4] < 0)
                        $errors[] = "'{$item[1]}' - số lượng khả dụng và số lượng khả dụng tối thiểu không được nhỏ hơn 0";
                }

                if (!empty($item[0])) {
                    $existing = $this->repository->findById($item[0]);
                    if (empty($existing))
                        $errors[] = "'{$item[1]}' - ID không tồn tại trong hệ thống";
                }

                if (empty($item[1]))
                    $errors[] = "'{$item[1]}' - tên loại hồ sơ không được để trống";

                if (empty($item[2]))
                    $errors[] = "'{$item[1]}' - đơn vị tính không được để trống";
            }
            unset($item);

            if (!empty($errors))
                throw new \Exception(implode('<br>', $errors));

            $userId = auth()->id();  // Cache user ID

            foreach ($data as $item) {
                $itemData = [
                    'name' => $item[1],
                    'unit' => $item[2],
                    'quantity' => $item[3],
                    'quantity_limit' => $item[4],
                    'created_by' => $userId
                ];

                if (isset($item[0]) && !empty($item[0])) {
                    $itemData['id'] = $item[0];
                    $this->repository->update($itemData);
                } else {
                    $this->repository->store($itemData);
                }
            }
        }, true);
    }
}
