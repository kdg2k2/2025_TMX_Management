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
        Schema::create('device_loans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('device_id')->comment('thiết bị')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('created_by')->comment('người mượn')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('approved_by')->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->text('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
            $table->date('borrowed_date')->comment('ngày mượn');
            $table->date('expected_return_at')->comment('ngày dự kiến trả');
            $table->timestamp('approved_at')->comment('thời gian duyệt')->nullable();
            $table->timestamp('returned_at')->comment('thời gian trả')->nullable();
            $table->enum('status', [
                'pending',  // chờ phê duyệt
                'approved',  // đã duyệt
                'rejected',  // từ chối
                'returned',  // đã trả
            ])->default('pending');
            $table->string('note')->comment('ghi chú')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_loans');
    }
};
