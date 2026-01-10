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
        // mã kaspersky
        Schema::create('kaspersky_codes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('code')->unique()->comment('mã Kaspersky');

            $table->integer('total_quantity')->comment('tổng số lượng cho phép sử dụng');
            $table->integer('used_quantity')->default(0)->comment('số lượt đã sử dụng');

            $table->integer('valid_days')->comment('thời hạn sử dụng (tính theo ngày)');
            $table->boolean('is_quantity_exceeded')->default(false)->comment('đã đạt giới hạn số lượng');
            $table->boolean('is_expired')->default(false)->comment('đã hết hạn');
            $table->date('started_at')->nullable()->comment('ngày bắt đầu tính hạn sử dụng');
            $table->date('expired_at')->nullable()->comment('ngày hết hạn');
            $table->string('path')->nullable()->comment('ảnh mua mã');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaspersky_codes');
    }
};
