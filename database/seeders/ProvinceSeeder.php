<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Province::truncate();
        Province::insert([
            [
                'code' => '01',
                'name' => 'Thành phố Hà Nội',
            ],
            [
                'code' => '02',
                'name' => 'Tỉnh Bắc Ninh',
            ],
            [
                'code' => '03',
                'name' => 'Tỉnh Quảng Ninh',
            ],
            [
                'code' => '04',
                'name' => 'Thành phố Hải Phòng',
            ],
            [
                'code' => '05',
                'name' => 'Tỉnh Hưng Yên',
            ],
            [
                'code' => '06',
                'name' => 'Tỉnh Ninh Bình',
            ],
            [
                'code' => '07',
                'name' => 'Tỉnh Cao Bằng',
            ],
            [
                'code' => '08',
                'name' => 'Tỉnh Tuyên Quang',
            ],
            [
                'code' => '09',
                'name' => 'Tỉnh Lào Cai',
            ],
            [
                'code' => '10',
                'name' => 'Tỉnh Thái Nguyên',
            ],
            [
                'code' => '11',
                'name' => 'Tỉnh Lạng Sơn',
            ],
            [
                'code' => '12',
                'name' => 'Tỉnh Phú Thọ',
            ],
            [
                'code' => '13',
                'name' => 'Tỉnh Điện Biên',
            ],
            [
                'code' => '14',
                'name' => 'Tỉnh Lai Châu',
            ],
            [
                'code' => '15',
                'name' => 'Tỉnh Sơn La',
            ],
            [
                'code' => '16',
                'name' => 'Tỉnh Thanh Hóa',
            ],
            [
                'code' => '17',
                'name' => 'Tỉnh Nghệ An',
            ],
            [
                'code' => '18',
                'name' => 'Tỉnh Hà Tĩnh',
            ],
            [
                'code' => '19',
                'name' => 'Tỉnh Quảng Trị',
            ],
            [
                'code' => '20',
                'name' => 'Thành phố Huế',
            ],
            [
                'code' => '21',
                'name' => 'Thành phố Đà Nẵng',
            ],
            [
                'code' => '22',
                'name' => 'Tỉnh Quảng Ngãi',
            ],
            [
                'code' => '23',
                'name' => 'Tỉnh Khánh Hòa',
            ],
            [
                'code' => '24',
                'name' => 'Tỉnh Gia Lai',
            ],
            [
                'code' => '25',
                'name' => 'Tỉnh Đắk Lắk',
            ],
            [
                'code' => '26',
                'name' => 'Tỉnh Lâm Đồng',
            ],
            [
                'code' => '27',
                'name' => 'Tỉnh Tây Ninh',
            ],
            [
                'code' => '28',
                'name' => 'Tỉnh Đồng Nai',
            ],
            [
                'code' => '29',
                'name' => 'Thành phố Hồ Chí Minh',
            ],
            [
                'code' => '30',
                'name' => 'Tỉnh Vĩnh Long'
            ],
            [
                'code' => '31',
                'name' => 'Tỉnh Đồng Tháp',
            ],
            [
                'code' => '32',
                'name' => 'Tỉnh An Giang',
            ],
            [
                'code' => '33',
                'name' => 'Thành phố Cần Thơ',
            ],
            [
                'code' => '34',
                'name' => 'Tỉnh Cà Mau',
            ],
        ]);
    }
}
