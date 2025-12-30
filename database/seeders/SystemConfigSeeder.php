<?php

namespace Database\Seeders;

use App\Models\SystemConfig;
use App\Models\User;
use Illuminate\Database\Seeder;

class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userHuanId = User::where('name', 'like', '%Phạm Văn Huân%')->first()->id;
        $userOanhId = User::where('name', 'like', '%Vũ Thị Kim Oanh%')->first()->id;
        $arr = [
            [
                ['key' => 'dossier_plan_handover_id'],
                [
                    'value' => $userHuanId,
                    'unit' => 'int',
                    'description' => 'ID user bên giao của biên bản kế hoạch hồ sơ ngoại nghiệp - Phạm Văn Huân',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
            [
                ['key' => 'dossier_handover_received_by'],
                [
                    'value' => $userHuanId,
                    'unit' => 'int',
                    'description' => 'ID user bên nhận của biên bản bàn giao hồ sơ ngoại nghiệp - Phạm Văn Huân',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
            [
                ['key' => 'professional_record_plan_handover_id'],
                [
                    'value' => $userOanhId,
                    'unit' => 'int',
                    'description' => 'ID user bên giao của biên bản kế hoạch hồ sơ chuyên môn - Vũ Thị Kim Oanh',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
            [
                ['key' => 'professional_record_handover_received_by'],
                [
                    'value' => $userOanhId,
                    'unit' => 'int',
                    'description' => 'ID user bên nhận của biên bản bàn giao hồ sơ chuyên môn - Vũ Thị Kim Oanh',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ],
        ];

        foreach ($arr as $item)
            SystemConfig::updateOrCreate(...$item);
    }
}
