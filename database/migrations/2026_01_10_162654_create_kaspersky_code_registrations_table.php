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
        // đăng ký mã kaspersky
        Schema::create('kaspersky_code_registrations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người đăng ký')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('reason')->comment('lý do đăng ký');
            $table->enum('type', [
                'personal',  // máy cá nhân
                'company',  // máy công ty
                'both',  // cả 2
            ])->comment('kiểu đăng ký');
            $table->enum('status', [
                'pending',  // chờ phê duyệt
                'approved',  // đã duyệt
                'rejected',  // từ chối
            ])->default('pending')->comment('trạng thái đăng ký');
            $table->foreignId('device_id')->nullable()->comment('thiết bị')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('approved_at')->nullable()->comment('thời gian duyệt');
            $table->foreignId('approved_by')->nullable()->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->string('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaspersky_code_registrations');
    }
};
