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
        // chi tiết bảng xuất lưới
        Schema::create('work_timesheet_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('work_timesheet_id')->comment('khóa ngoại xuất lưới')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            // Thông tin cơ bản của user
            $table->foreignId('user_id')->comment('khóa ngoại tài khoản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->comment('tên');
            $table->string('department')->comment('phòng ban');
            $table->integer('position_id')->comment('chức vụ - chỉ dùng để sort user trong phòng ban');

            $table->integer('salary_level')->default(0)->comment('Mức lương');
            $table->integer('violation_penalty')->default(0)->comment('Mức tiêu chí');
            $table->integer('social_insurance_deduction')->default(0)->comment('Tiền BHXH (8%)');
            $table->integer('health_insurance_deduction')->default(0)->comment('Tiền BHYT (1.5%)');
            $table->integer('unemployment_insurance_deduction')->default(0)->comment('Tiền BHTN (1%)');
            $table->integer('total_tax_deduction')->default(0)->comment('Tổng tiền khấu trừ bảo hiểm');
            $table->integer('allowance_contact')->default(0)->comment('Phụ cấp liên lạc');
            $table->integer('allowance_meal')->default(0)->comment('Phụ cấp ăn ca');
            $table->integer('allowance_position')->default(0)->comment('Phụ cấp chức vụ');
            $table->integer('allowance_fuel')->default(0)->comment('Phụ cấp xăng xe');
            $table->integer('allowance_transport')->default(0)->comment('Phụ cấp đi lại');

            // Công và chấm công
            $table->float('proposed_work_days')->default(0)->comment('Công bộ phận đề xuất');
            $table->float('valid_attendance_count')->default(0)->comment('Số lần chấm công hợp lệ');
            $table->float('invalid_attendance_count')->default(0)->comment('Số lần chấm công không hợp lệ');
            $table->float('invalid_attendance_rate')->default(0)->comment('Tỷ lệ chấm công không hợp lệ');
            $table->integer('late_morning_count')->default(0)->comment('Số lần chấm công muộn buổi sáng');
            $table->integer('early_morning_count')->default(0)->comment('Số lần chấm công sớm buổi sáng');
            $table->integer('late_afternoon_count')->default(0)->comment('Số lần chấm công muộn buổi chiều');
            $table->integer('early_afternoon_count')->default(0)->comment('Số lần chấm công sớm buổi chiều');
            $table->float('avg_late_minutes')->nullable()->comment('Trung bình phút chấm công muộn');

            // Ngoài giờ
            $table->float('overtime_salary_rate')->default(0)->comment('Mức lương ngoài giờ (mức lương / công bộ phận đề xuất) / 2');
            $table->integer('overtime_evening_count')->default(0)->comment('Số công ngoài giờ buổi tối');
            $table->integer('overtime_weekend_count')->default(0)->comment('Số công ngoài giờ T7 CN');
            $table->integer('overtime_total_count')->default(0)->comment('Tổng số công ngoài giờ');
            $table->integer('overtime_total_amount')->default(0)->comment('Tổng tiền công ngoài giờ');

            // Công tác & nghỉ phép
            $table->integer('business_trip_system_count')->default(0)->comment('Tổng số công đi công tác - hệ thống tính');
            $table->integer('business_trip_manual_count')->default(0)->comment('Tổng số công đi công tác - rà soát thủ công');
            $table->float('leave_days_with_permission')->default(0)->comment('Tổng số ngày nghỉ phép');
            $table->float('leave_days_without_permission')->default(0)->comment('Tổng số ngày nghỉ không phép');
            $table->float('total_leave_days_in_year')->default(0)->comment('Tổng số ngày đã nghỉ trong năm');
            $table->float('max_paid_leave_days_per_year')->default(0)->comment('số ngày nghỉ có lương tối đa của trong năm');
            $table->float('warning_count')->default(0)->comment('Tổng số lần bị cảnh báo');

            // Đánh giá
            $table->enum('department_rating', ['A', 'B', 'C', 'D'])->nullable()->comment('Đánh giá của phòng');
            $table->enum('council_rating', ['A', 'B', 'C', 'D'])->nullable()->comment('Đánh giá của hội đồng');

            // Top muộn
            $table->boolean('is_latest_arrival')->default(false)->comment('Người muộn làm nhất (3 người muộn nhất trong tháng sẽ check true)');

            // Đánh giá nội quy / đào tạo
            $table->integer('rule_b_count')->default(0)->comment('Số lần bị đánh giá nội quy B');
            $table->integer('rule_c_count')->default(0)->comment('Số lần bị đánh giá nội quy C');
            $table->integer('rule_d_count')->default(0)->comment('Số lần bị đánh giá nội quy D');
            $table->integer('training_a_count')->default(0)->comment('Số lần đánh giá đào tạo A');
            $table->integer('training_b_count')->default(0)->comment('Số lần bị đánh giá đào tạo B');
            $table->integer('training_c_count')->default(0)->comment('Số lần bị đánh giá đào tạo đào tạo C');

            // Tiền & log
            $table->integer('deduction_amount')->default(0)->comment('Số tiền trừ');
            $table->integer('total_received_salary')->default(0)->comment('Tổng lương nhận');
            $table->json('detail_business_trip_and_leave_days')->nullable()->comment('Mảng các ngày công tác và nghỉ trong tháng');

            $table->string('note')->nullable()->comment('ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_timesheet_details');
    }
};
