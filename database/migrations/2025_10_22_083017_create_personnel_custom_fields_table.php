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
        Schema::create('personnel_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->comment('tên cột hiển thị');
            $table->string('field')->unique()->comment('tên cột lưu db');
            $table->enum('type', ['text', 'date', 'datetime', 'number']);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('người tạo-cập nhật');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_custom_fields');
    }
};
