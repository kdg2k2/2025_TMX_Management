<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arr = [
            ['name' => 'Hà Nội (HAN), Sân bay QT Nội Bài'],
            ['name' => 'Hồ Chí Minh (SGN), Sân bay QT Tân Sơn Nhất'],
            ['name' => 'Đà Nẵng (DAD), Sân bay QT Đà Nẵng'],
            ['name' => 'Quảng Ninh (VDO), Sân bay QT Vân Đồn'],
            ['name' => 'Hải Phòng (HPH), Sân bay QT Cát Bi'],
            ['name' => 'Nghệ An (VII), Sân bay QT Vinh'],
            ['name' => 'Huế (HUI), Sân bay QT Phú Bài'],
            ['name' => 'Khánh Hòa (CXR), Sân bay QT Cam Ranh'],
            ['name' => 'Lâm Đồng (DLI), Sân bay QT Liên Khương'],
            ['name' => 'Bình Định (UIH), Sân bay QT Phù Cát'],
            ['name' => 'Cần Thơ (VCA), Sân bay QT Cần Thơ'],
            ['name' => 'Kiên Giang (PQC), Sân bay QT Phú Quốc'],
            ['name' => 'Điện Biên (DIN), Sân bay Điện Biên Phủ'],
            ['name' => 'Thanh Hóa (THD), Sân bay Thọ Xuân'],
            ['name' => 'Quảng Bình (VDH), Sân bay Đồng Hới'],
            ['name' => 'Quảng Nam (VCL), Sân bay Chu Lai'],
            ['name' => 'Phú Yên (TBB), Sân bay Tuy Hòa'],
            ['name' => 'Gia Lai (PXU), Sân bay Pleiku'],
            ['name' => 'Đắk Lắk (BMV), Sân bay Buôn Mê Thuột'],
            ['name' => 'Kiên Giang (VKG), Sân bay Rạch Giá'],
            ['name' => 'Cà Mau (CAH), Sân bay Cà Mau'],
            ['name' => 'Bà Rịa – Vũng Tàu (VCS), Sân bay Côn Đảo'],
        ];
        foreach ($arr as $item) {
            Airport::updateOrCreate([
                'name' => $item['name'],
            ], $item);
        }
    }
}
