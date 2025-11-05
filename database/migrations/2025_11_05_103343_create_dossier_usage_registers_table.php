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
        // đăng ký sử dụng hồ sơ ngoại nghiệp
        Schema::create('dossier_usage_registers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dossier_plan_id')->comment('khóa ngoại kế hoạch')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('registered_by')->comment('khóa ngoại người đăng ký')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('handover_date')->nullable()->comment('ngày bàn giao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_usage_registers');
    }
};
