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
        Schema::create('personnels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->comment('họ và tên');
            $table->foreignId('personnel_unit_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('educational_level')->comment('trình độ học vấn');
            $table->boolean('is_it_displayed_in_summary')->default(true)->comment('hiển thị trong tổng hợp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
