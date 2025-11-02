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
        // bảng xuất lưới
        Schema::create('work_timesheets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('year')->comment('năm');
            $table->integer('month')->comment('tháng');

            $table->integer('total_holiday_days')->default(0)->comment('Tổng số ngày nghỉ lễ');
            $table->integer('total_power_outage_days')->default(0)->comment('Tổng số ngày mất điện');
            $table->integer('total_compensated_days')->default(0)->comment('Tổng số ngày làm bù');
            $table->json('days_details')->nullable()->comment('Mảng các ngày mất điện, nghỉ lễ, làm bù trong tháng');

            $table->string('original_path')->comment('đường dẫn file gốc');
            $table->string('calculated_path')->nullable()->comment('đường dẫn file đã tính toán');

            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_timesheets');
    }
};
