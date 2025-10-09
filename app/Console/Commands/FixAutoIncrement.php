<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAutoIncrement extends Command
{
    protected $signature = 'db:fix-auto-increment';
    protected $description = 'Reset AUTO_INCREMENT for all MySQL tables to match the current max(id)';

    public function handle()
    {
        // Lấy danh sách tất cả bảng trong DB hiện tại
        $database = config('database.connections.mysql.database');
        $tables = DB::select("SHOW TABLES");

        if (empty($tables)) {
            $this->warn("⚠ No tables found in database: {$database}");
            return 0;
        }

        foreach ($tables as $tableObj) {
            $tableName = array_values((array)$tableObj)[0];

            // Kiểm tra bảng có cột id không
            $hasId = DB::select("SHOW COLUMNS FROM `{$tableName}` LIKE 'id'");
            if (empty($hasId)) {
                continue;
            }

            // Lấy MAX(id)
            $maxId = DB::table($tableName)->max('id') ?? 0;
            $nextId = $maxId + 1;

            // Reset AUTO_INCREMENT
            DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = {$nextId}");

            $this->info("✔ Reset AUTO_INCREMENT for {$tableName} to {$nextId}");
        }

        $this->info("✨ All AUTO_INCREMENT values fixed successfully!");
        return 0;
    }
}
