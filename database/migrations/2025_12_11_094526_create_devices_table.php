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
        // thiết bị
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('device_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->comment('tên thiết bị');
            $table->string('code')->unique()->comment('mã thiết bị (hệ thống)');
            $table->string('seri')->nullable()->comment('seri thiết bị nếu có');
            $table->enum('current_status', [
                'normal',  // bình thường
                'broken',  // hỏng
                'faulty',  // lỗi
                'lost',  // thất lạc
                'loaned',  // cho mượn
                'under_repair',  // sửa chữa
                'stored',  // lưu kho
            ])->default('normal')->comment('tình trạng hiện tại');
            $table->enum('previous_status', [
                'normal',  // bình thường
                'broken',  // hỏng
                'faulty',  // lỗi
                'lost',  // thất lạc
                'loaned',  // cho mượn
                'under_repair',  // sửa chữa
                'stored',  // lưu kho
            ])->nullable()->comment('tình trạng trước đó');
            $table->string('current_location')->nullable()->comment('Vị trí hiện tại');
            $table->foreignId('user_id')->nullable()->comment('người sử dụng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
