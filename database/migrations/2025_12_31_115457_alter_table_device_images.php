<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('device_images', function (Blueprint $table) {
            // Drop FK cũ
            $table->dropForeign(['device_id']);

            // Tạo lại FK đúng
            $table
                ->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('device_images', function (Blueprint $table) {
            // Drop FK mới
            $table->dropForeign(['device_id']);

            // Rollback về cấu hình cũ
            $table
                ->foreign('device_id')
                ->references('id')
                ->on('devices')
                ->cascadeOnDelete();
        });
    }
};
