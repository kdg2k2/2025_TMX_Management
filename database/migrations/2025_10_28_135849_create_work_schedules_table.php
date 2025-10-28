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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('address')->comment('địa điểm');
            $table->date('from_date')->comment('ngày bắt đầu');
            $table->date('to_date')->comment('ngày kết thúc');
            $table->string('content')->comment('nội dung công tác');
            $table->enum('type_program', ['contract', 'other'])->comment('kiểu chương trình');
            $table->foreignId('contract_id')->nullable()->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program')->nullable()->comment('tên chương trình khác');
            $table->string('clue')->nullable()->comment('đàu mối');
            $table->string('participants')->nullable()->comment('thành phần tham gia');
            $table->string('note')->nullable()->comment('ghi chú');

            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->comment('trạng thái phê duyệt');
            $table->string('approval_note')->comment('ghi chú phê duyệt');
            $table->date('approval_date')->comment('ngày phê duyệt');
            $table->foreignId('approved_by')->nullable()->comment('khóa ngoại người duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->dateTime('return_datetime')->nullable()->comment('thời gian về');
            $table->enum('return_approval_status', ['none', 'pending', 'approved', 'rejected'])->default('none')->comment('trạng thái duyệt kết thúc công tác');
            $table->text('return_approval_note')->nullable()->comment('ghi chú duyệt về');
            $table->date('return_approval_date')->comment('ngày phê duyệt');
            $table->foreignId('return_approved_by')->nullable()->comment('khóa ngoại người duyệt về')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->boolean('is_completed')->default(false)->comment('đã kết thúc');
            $table->integer('total_trip_days')->nullable()->comment('Tổng số ngày');
            $table->integer('total_work_days')->nullable()->comment('Tổng số công');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
