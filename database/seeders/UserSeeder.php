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
        User::truncate();

        $stringHandlerService = app(\App\Services\StringHandlerService::class);
        $arr = array_map(function ($item) use ($stringHandlerService) {
            $item['department_id'] = Department::inRandomOrder()->first()->id;
            $item['position_id'] = Position::inRandomOrder()->first()->id;
            $item['job_title_id'] = JobTitle::inRandomOrder()->first()->id;
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
                ]
            ],
            ...array_map(function ($item) {
                return [
                    'name' => $item,
                    'is_salary_counted' => true,
                    'salary_level' => 6000000,
                    'allowance_meal' => 630000,
                ];
            }, [
                'Lê Sỹ Doanh',
                'Phạm Văn Huân',
                'Vũ Thị Kim Oanh',
                'Tòng Thị Hoài Thu',
                'Lê Ngọc Trọng',
                'Ma Đình Tú',
            ])
        ]);

        foreach ($arr as $item) {
            $subEmails = $item['sub_emails'];
            unset($item['sub_emails']);
            $user = User::create($item);
            $user->subEmails()->createMany($subEmails);
        }

        if ($this->isLocal())
            User::factory()->count(10)->create();
    }
}
