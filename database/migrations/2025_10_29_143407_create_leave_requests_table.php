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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('from_date')->comment('ngày bắt đầu');
            $table->date('to_date')->comment('ngày kết thúc');
            $table->string('reason')->comment('lý do');
            $table->enum('type', ['both', 'morning', 'afternoon'])->comment('kiểu đăng ký');
            $table->double('total_leave_days')->comment('Tổng số ngày');

            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->comment('trạng thái phê duyệt');
            $table->string('approval_note')->nullable()->comment('ghi chú phê duyệt');
            $table->date('approval_date')->nullable()->comment('ngày phê duyệt');
            $table->foreignId('approved_by')->nullable()->comment('khóa ngoại người duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->enum('adjust_approval_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->comment('trạng thái duyệt điều chỉnh nghỉ phép');
            $table->text('adjust_approval_note')->nullable()->comment('ghi chú duyệt điều chỉnh nghỉ phép');
            $table->date('adjust_approval_date')->nullable()->comment('ngày phê duyệt điều chỉnh nghỉ phép');
            $table->foreignId('adjust_approved_by')->nullable()->comment('khóa ngoại người duyệt điều chỉnh nghỉ phép')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->json('before_adjust')->nullable()->comment('Dữ liệu trước điều chỉnh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
