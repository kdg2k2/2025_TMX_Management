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
        Schema::create('user_warnings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('người bị cảnh báo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('warning_date')->comment('ngày cảnh báo');
            $table->enum('type', ['job', 'work_schedule'])->comment('kiểu cảnh báo');
            $table->json('detail')->nullable()->comment('thông tin chi tiết cảnh báo');

            $table->unique(['user_id', 'warning_date']);  // một người chỉ bị cảnh báo 1 lần/ngày
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_warnings');
    }
};
