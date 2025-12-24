<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\JobTitle;
use App\Models\Position;
use App\Models\User;
use App\Traits\CheckLocalTraits;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use CheckLocalTraits;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stringHandlerService = app(\App\Services\StringHandlerService::class);
        $arr = array_map(function ($item) use ($stringHandlerService) {
            if (!isset($item['department_id']))
                $item['department_id'] = Department::inRandomOrder()->first()->id;
            if (!isset($item['position_id']))
                $item['position_id'] = 5;
            if (!isset($item['job_title_id']))
                $item['job_title_id'] = 11;
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');

            $lowername = strtolower(str_replace(' ', '', $stringHandlerService->removeAccents($item['name'])));
            if (!isset($item['email']))
                $item['email'] = $lowername . '@tanmaixanh.vn';
            if (!isset($item['password']))
                $item['password'] = bcrypt('123456');

            // Tách tên thành mảng các từ (loại bỏ nhiều khoảng trắng nếu có)
            $nameParts = preg_split('/\s+/', trim($item['name']));
            $firstWord = $nameParts[0] ?? '';  // từ đầu
            $lastWord = $nameParts[count($nameParts) - 1] ?? '';  // từ cuối

            // Nếu chỉ có 1 từ, dùng chính nó cho cả first và last (hoặc bạn có thể quyết khác)
            if ($firstWord === $lastWord) {
                $local = strtolower($stringHandlerService->removeAccents($firstWord));
            } else {
                // ghép last + first, loại bỏ dấu và viết thường
                $local = strtolower($stringHandlerService->removeAccents($lastWord . $firstWord));
            }

            // Tạo email phụ
            if (!isset($item['sub_emails']))
                $item['sub_emails'] = [
                    [
                        'email' => $lowername . '@ifee.edu.vn',
                    ],
                    [
                        'email' => $local . '.xmg@xuanmaijsc.vn',
                    ],
                ];

            return $item;
        }, [
            [
                'name' => 'Super Admin',
                'email' => 'xmg@ifee.edu.vn',
                'password' => bcrypt('Xmg@@2025'),
                'sub_emails' => [
                    [
                        'email' => 'dangnguyen.xmg@xuanmaijsc.vn',
                    ]
                ],
            ],
            [
                'name' => 'Lê Sỹ Doanh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Phạm Văn Huân',
                'is_salary_counted' => false,
                'department_id' => 2,
                'job_title_id' => 5,
            ],
            [
                'name' => 'Vũ Thị Kim Oanh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Kiều Đăng Anh',
                'is_salary_counted' => true,
                'department_id' => 1,
                'position_id' => 1,
                'job_title_id' => 2,
            ],
            [
                'name' => 'Trần Thị Bích Ngọc',
                'is_salary_counted' => false,
                'position_id' => 1,
                'department_id' => 2,
                'job_title_id' => 10,
            ],
            [
                'name' => 'Tòng Thị Hoài Thu',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lê Ngọc Trọng',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Ma Đình Tú',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Trần Văn Hải',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Phạm Văn Duẩn',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Thị Ánh Vân',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Văn Thị',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lê Sỹ Hòa',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Bùi Trung Hiếu',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lã Nguyên Khang',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Thị Mai Dương',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Văn Hiếu',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Phạm Quang Dương',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hoàng Văn Khiên',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Vũ Thị Thìn',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Văn Tùng',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Hữu Văn',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Văn Tuyên',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lê Thị Anh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Trần Lê Kiều Oanh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Song Anh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Đào Thị Dương',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hoàng Thị Ngọc',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Trần Thị Minh',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hồ Thu Phương',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Vũ Trần Thúy Nhi',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lỳ Pé Mư',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Vương Thị Hà',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Sơn Hà',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hoàng Thị Kiều Diễm',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Phan Ngọc Sơn',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hoàng Văn Thành',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Khả Đăng',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Lê Hữu Cường',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Xuân Việt',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Hoàng Hương Giang',
                'is_salary_counted' => false,
            ],
            [
                'name' => 'Nguyễn Văn Ánh',
                'is_salary_counted' => false,
            ],
        ]);

        foreach ($arr as $item) {
            $subEmails = $item['sub_emails'];
            unset($item['sub_emails']);
            $user = User::updateOrCreate(['email' => $item['email']], $item);
            $user->subEmails()->delete();
            $user->subEmails()->createMany($subEmails);
        }
    }
}
