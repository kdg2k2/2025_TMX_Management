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
        // các user nhận mail task tự động
        Schema::create('task_schedule_emails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('task_schedule_id')->comment('khóa ngoại bảng task_schedules')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('khóa ngoại bảng users')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['task_schedule_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_schedule_emails');
    }
};
