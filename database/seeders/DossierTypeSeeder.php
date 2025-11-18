<?php

namespace Database\Seeders;

use App\Models\DossierType;
use Illuminate\Database\Seeder;

class DossierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr =
            [
                [
                    'name' => 'Giấy đi đường',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Nhật trình xe',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Phiếu khảo sát',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Danh sách phỏng vấn',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Danh sách chi tiền',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Biên bản làm việc',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Bảng kê chi tiền',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Biên bản thống nhất số liệu',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Bộ phiếu điều tra OTC',
                    'unit' => 'Bộ',
                ],
                [
                    'name' => 'Phiếu phỏng vấn',
                    'unit' => 'Phiếu',
                ],
                [
                    'name' => 'Danh sách xác nhận phỏng vấn',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Danh sách xác nhận khảo sát',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Giấy xác nhận công tác',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Danh sách người cung cấp thông tin',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Biên Bản thống nhất số liệu ngoại nghiệp',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'Biên bản sơ thám',
                    'unit' => 'Bản',
                ],
                [
                    'name' => 'OTC',
                    'unit' => 'Tờ',
                ],
                [
                    'name' => 'Giấy xác nhận khối lượng của chủ rừng',
                    'unit' => 'Tờ',
                ],
            ];

        DossierType::truncate();
        app(\App\Services\DossierTypeService::class)->insert(array_map(function ($i) {
            $i['created_by'] = 1;
            return $i;
        }, $arr));
    }
}
