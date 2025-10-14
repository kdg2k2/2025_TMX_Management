<?php

namespace Database\Seeders;

use App\Models\FileExtension;
use Illuminate\Database\Seeder;

class FileExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FileExtension::truncate();
        FileExtension::insert([
            [
                'extension' => 'docx',
            ],
            [
                'extension' => 'doc',
            ],
            [
                'extension' => 'xlsx',
            ],
            [
                'extension' => 'xls',
            ],
            [
                'extension' => 'xlsm',
            ],
            [
                'extension' => 'pdf',
            ],
            [
                'extension' => 'rar',
            ],
            [
                'extension' => 'zip',
            ],
        ]);
    }
}
