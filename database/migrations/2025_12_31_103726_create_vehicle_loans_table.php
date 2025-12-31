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
        // mượn xe
        Schema::create('vehicle_loans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('vehicle_id')->comment('phương tiện')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('created_by')->comment('người mượn')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('destination')->nullable()->comment('Điểm đến');
            $table->string('work_content')->nullable()->comment('Nội dung công việc');
            $table->dateTime('vehicle_pickup_time')->comment('thời gian lấy xe');
            $table->date('estimated_vehicle_return_date')->comment('ngày dự kiến trả trả');

            $table->timestamp('approved_at')->nullable()->comment('thời gian duyệt');
            $table->foreignId('approved_by')->nullable()->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->text('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
            $table->string('note')->comment('ghi chú')->nullable();

            $table->string('before_front_image')->comment('Ảnh hiện trạng xe phía trước (khi mượn)');
            $table->string('before_rear_image')->comment('Ảnh hiện trạng xe phía sau (khi mượn)');
            $table->string('before_left_image')->comment('Ảnh hiện trạng xe phía trái (khi mượn)');
            $table->string('before_right_image')->comment('Ảnh hiện trạng xe phía phải (khi mượn)');
            $table->string('before_odometer_image')->comment('Ảnh hiện trạng công tơ mét (khi mượn)');

            $table->string('return_front_image')->nullable()->comment('Ảnh hiện trạng xe phía trước (khi trả)');
            $table->string('return_rear_image')->nullable()->comment('Ảnh hiện trạng xe phía sau (khi trả)');
            $table->string('return_left_image')->nullable()->comment('Ảnh hiện trạng xe phía trái (khi trả)');
            $table->string('return_right_image')->nullable()->comment('Ảnh hiện trạng xe phía phải (khi trả)');
            $table->string('return_odometer_image')->nullable()->comment('Ảnh hiện trạng công tơ mét (khi trả)');

            $table->integer('current_km')->comment('Số km hiện trạng');
            $table->integer('return_km')->nullable()->comment('Số km khi trả');

            $table->timestamp('returned_at')->nullable()->comment('thời gian trả');
            $table->enum('status', [
                'pending',  // chờ phê duyệt
                'approved',  // đã duyệt
                'rejected',  // từ chối
                'returned',  // đã trả
            ])->default('pending');
            $table->enum('vehicle_status_return', [
                'ready',  // sẵn sàng
                'unwashed',  // chưa rửa
                'broken',  // hỏng
                'faulty',  // lỗi
                'lost',  // thất lạc
            ])->nullable()->comment('tình trạng phương tiện khi trả');
            $table->integer('fuel_cost')->nullable()->comment('Chi phí xăng xe');
            $table->foreignId('fuel_cost_paid_by')->nullable()->comment('Người trả chi phí xăng xe')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('maintenance_cost')->nullable()->comment('Chi phí bảo dưỡng');
            $table->foreignId('maintenance_cost_paid_by')->nullable()->comment('Người trả chi phí bảo dưỡng')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_loans');
    }
};
