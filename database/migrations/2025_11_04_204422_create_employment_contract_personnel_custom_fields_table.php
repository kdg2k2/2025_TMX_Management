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
        Schema::create('employment_contract_personnel_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('z_index')->default(1)->comment('dộ ưu tiên');
            $table->string('name')->unique()->comment('tên cột hiển thị');
            $table->string('field')->unique()->comment('tên cột lưu db');
            $table->enum('type', ['text', 'date', 'number']);
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_contract_personnel_custom_fields');
    }
};
