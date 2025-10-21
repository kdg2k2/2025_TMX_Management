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
        Schema::create('personnel_file_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->comment('tên');
            $table->string('description')->nullable()->comment('mô tả');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_file_types');
    }
};
