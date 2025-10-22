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
        Schema::create('personnel_pivot_personnel_custom_fields', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('personnel_id')->comment('khóa ngoại nhân sự')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table
                ->foreignId('personnel_custom_field_id')
                ->comment('khóa ngoại cột thông tin nhân sự tự cấu hình')
                ->constrained('personnel_custom_fields', 'id', 'fk_personnel_custom_field')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_pivot_personnel_custom_fields');
    }
};
