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
        // người mô tả xây dựng phần mềm
        Schema::create('build_software_business_analysts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('build_software_id')->comment('khóa ngoại xây dựng phần mềm')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('khóa ngoại người dùng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_software_business_analysts');
    }
};
