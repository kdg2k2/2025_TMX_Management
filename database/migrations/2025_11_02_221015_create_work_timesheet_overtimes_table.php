<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // bảng chấm công làm thêm giờ
        Schema::create('work_timesheet_overtimes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('work_timesheet_id')->comment('khóa ngoại xuất lưới')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('path')->comment('đường dẫn file gốc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_timesheet_overtimes');
    }
};
