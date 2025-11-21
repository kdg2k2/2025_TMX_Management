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
        Schema::create('professional_record_minutes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('professional_record_plan_id')->nullable()->comment('khóa ngoại kế hoạch')->constrained('professional_record_plans', 'id', 'professional_record_minutes_plan_fk')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('professional_record_handover_id')->nullable()->comment('khóa ngoại bàn giao')->constrained('professional_record_handovers', 'id', 'professional_record_minutes_handover_fk')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('professional_record_usage_register_id')->nullable()->comment('khóa ngoại đăng ký')->constrained('professional_record_usage_registers', 'id', 'professional_record_minutes_usage_register_fk')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected'])->default('draft')->comment('Trạng thái biên bản');
            $table->enum('type', ['plan', 'handover', 'usage_register'])->default('plan')->comment('Loại biên bản');
            $table->text('path')->comment('Đường dẫn lưu trữ file biên bản');
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('approved_at')->nullable()->comment('Thời gian phê duyệt');
            $table->text('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->text('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_minutes');
    }
};
