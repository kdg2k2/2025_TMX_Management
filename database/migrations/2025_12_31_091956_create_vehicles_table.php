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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('brand')->comment('Hãng xe');
            $table->string('license_plate')->unique()->comment('Biển số xe');
            $table->integer('current_km')->comment('Số km hiện trạng');
            $table->integer('maintenance_km')->nullable()->comment('Số km đến hạn bảo dưỡng');
            $table->date('inspection_expired_at')->nullable()->comment('Hạn đăng kiểm');
            $table->date('liability_insurance_expired_at')->nullable()->comment('Hạn bảo hiểm trách nhiệm dân sự');
            $table->date('body_insurance_expired_at')->nullable()->comment('Hạn bảo hiểm thân vỏ');
            $table->enum('status', [
                'ready', // sẵn sàng
                'loaned', // cho mượn
            ])->default('ready')->comment('Trạng thái xe');
            $table->string('current_location')->nullable()->comment('Vị trí hiện tại');
            $table->foreignId('user_id')->nullable()->comment('người sử dụng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
