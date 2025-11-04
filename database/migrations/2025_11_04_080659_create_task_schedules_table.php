<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // task mail tự động
        Schema::create('task_schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('code')->unique();
            $table->string('name')->comment('tên lịch task');
            $table->string('description')->nullable()->comment('mô tả lịch task');
            $table->string('subject')->nullable()->comment('chủ đề email gửi tự động');
            $table->text('content')->nullable()->comment('nội dung email gửi tự động');
            $table->enum('frequency', ['daily', 'weekly', 'monthly'])->comment('tần suất gửi mail tự động');
            $table->string('cron_expression')->comment('biểu thức cron');  // VD: 0 0 * * *
            $table->timestamp('last_run_at')->nullable()->comment('thời gian chạy lần cuối');
            $table->timestamp('next_run_at')->nullable()->comment('thời gian chạy tiếp theo');
            $table->boolean('is_active')->default(true)->comment('trạng thái kích hoạt lịch task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_schedules');
    }
};
