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
        Schema::create('personnel_file_type_extensions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('type_id')->comment('khóa ngoại loại file')->constrained('personnel_file_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('extension_id')->comment('khóa ngoại loại định dạng')->constrained('file_extensions')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_file_type_extensions');
    }
};
