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
        // nhân sự trong hồ sơ lao động/bổ nhiệm
        Schema::create('employment_contract_personnels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('khóa ngoại người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->comment('họ tên');
            $table->string('citizen_identification_number')->nullable()->comment('số căn cước công dân');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_contract_personnels');
    }
};
