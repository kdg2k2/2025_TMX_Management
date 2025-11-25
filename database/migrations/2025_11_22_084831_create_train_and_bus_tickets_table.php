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
        // vé tàu xe
        Schema::create('train_and_bus_tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', ['contract', 'other'])->comment('kiểu đăng ký');
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program_name')->nullable()->comment('tên chương trình khác');
            $table->date('estimated_travel_time')->comment('thời gian đi dự kiến');
            $table->string('expected_departure')->comment('điểm khởi hành dự kiến');
            $table->string('expected_destination')->comment('điểm đến dự kiến');
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval')->comment('Trạng thái duyệt');
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
        Schema::dropIfExists('train_and_bus_tickets');
    }
};
