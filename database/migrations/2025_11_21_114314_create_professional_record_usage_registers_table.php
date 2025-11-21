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
        Schema::create('professional_record_usage_registers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('professional_record_plan_id')->comment('khóa ngoại kế hoạch')->constrained('professional_record_plans', 'id', 'usage_registers_plan_fk')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('registered_by')->comment('khóa ngoại người đăng ký')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('handover_date')->nullable()->comment('ngày bàn giao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_usage_registers');
    }
};
