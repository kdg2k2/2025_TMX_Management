<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key cũ
            $table->dropForeign(['job_title_id']);

            // Tạo lại foreign key với cascadeOnUpdate
            $table
                ->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop FK mới
            $table->dropForeign(['job_title_id']);

            // Rollback về FK cũ (không cascadeOnUpdate)
            $table
                ->foreign('job_title_id')
                ->references('id')
                ->on('job_titles')
                ->cascadeOnDelete();
        });
    }
};
