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
        // vé máy bay
        Schema::create('plane_tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', ['contract', 'other'])->comment('kiểu đăng ký');
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program_name')->nullable()->comment('tên chương trình khác');
            $table->dateTime('estimated_flight_time')->comment('thời gian bay dự kiến');
            $table->foreignId('airport_id')->comment('sân bay')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('airline_id')->comment('hãng bay')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plane_ticket_class_id')->comment('hạng vé')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('checked_baggage_allowances')->default(0)->comment('số cân hành lý ký gửi (kg)');
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
        Schema::dropIfExists('plane_tickets');
    }
};
