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
        // chi tiết bảng chấm công làm thêm giờ
        Schema::create('work_timesheet_overtime_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table
                ->foreignId('work_timesheet_overtime_id')
                ->comment('khóa ngoại bảng chấm công làm thêm giờ')
                ->constrained('work_timesheet_overtimes', 'id', 'fk_overtime_detail_overtime')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('khóa ngoại tài khoản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('overtime_evening_count')->default(0)->comment('Số công ngoài giờ buổi tối');
            $table->integer('overtime_weekend_count')->default(0)->comment('Số công ngoài giờ T7 CN');
            $table->integer('overtime_total_count')->default(0)->comment('Tổng số công ngoài giờ');
            $table->integer('leave_days_without_permission')->default(0)->comment('Tổng số ngày nghỉ không phép');
            $table->json('detail_leave_days_without_permission')->nullable()->comment('chi tiết các ngày nghỉ ko phép');
            $table->enum('department_rating', ['A', 'B', 'C', 'D'])->nullable()->comment('Đánh giá của phòng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_timesheet_overtime_details');
    }
};
