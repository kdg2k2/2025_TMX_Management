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
        // xây dựng phần mềm
        Schema::create('build_software', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->nullable()->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->comment('tên phần mềm');
            $table->enum('development_case', [
                'update',  // Cập nhật, chỉnh sửa, bổ sung tính năng trên các hệ thống đã được xây dựng
                'new',  // Xây dựng hệ thống/phần mềm/công cụ hoàn toàn mới
                'suddenly',  // Trường hợp phát sinh đột xuất
            ])->comment('trường hợp xây dựng phần mềm');
            $table->string('description')->nullable()->comment('mô tả phần mềm');
            $table->string('attachment')->nullable()->comment('File đính kèm');
            $table->enum('state', [
                'pending',  // chưa thực hiện
                'doing_business_analysis',  // đang phân tích nghiệp vụ
                'construction_planning',  // đang lên kế hoạch xây dựng
                'in_progress',  // đang thực hiện
                'completed',  // hoàn thành
            ])->default('pending')->comment('Tình trạng');
            $table->enum('status', [
                'pending',  // chờ duyệt
                'accepted',  // đã duyệt
                'rejected',  // từ chối
            ])->default('pending')->comment('Trạng thái');
            $table->string('rejection_reason')->nullable()->comment('lý do từ chối');
            $table->timestamp('rejected_at')->nullable()->comment('thời gian từ chối');
            $table->timestamp('accepted_at')->nullable()->comment('thời gian chấp nhận');
            $table->timestamp('completed_at')->nullable()->comment('thời gian hoàn thành');
            $table->foreignId('created_by')->comment('Người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('verify_by')->nullable()->comment('Người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('deadline')->nullable()->comment('Thời hạn');
            $table->date('start_date')->nullable()->comment('ngày bắt đầu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_software');
    }
};
