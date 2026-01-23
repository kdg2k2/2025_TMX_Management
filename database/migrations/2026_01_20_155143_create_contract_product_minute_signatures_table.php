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
        // chữ ký số biên bản sptg
        Schema::create('contract_product_minute_signatures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', [
                'draw',  // ký tay
                'text',  // nhập text
                'upload',  // chọn ảnh
                'profile',  // dùng chữ ký cá nhân
            ])->nullable()->comment('kiểu ký');  // dựa vào kiểu ký BE xử lý logic để lấy path ảnh
            $table->enum('status', [
                'pending',
                'signed',
            ])->default('pending')->comment('trạng thái ký');
            $table->foreignId('contract_product_minute_id')->constrained('contract_product_minutes', 'id', 'fk_cpms_cpm')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id', 'fk_cpms_u')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('signature_path')->nullable()->comment('đường dẫn chữ ký');
            $table->timestamp('signed_at')->nullable()->comment('thời điểm ký');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product_minute_signatures');
    }
};
