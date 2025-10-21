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
        Schema::create('personnel_units', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('khóa ngoại người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique()->comment('tên đơn vị');
            $table->string('short_name')->unique()->comment('tên đơn vị viết tắt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_units');
    }
};
