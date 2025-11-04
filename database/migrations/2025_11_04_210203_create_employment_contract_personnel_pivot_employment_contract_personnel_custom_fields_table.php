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
        Schema::create('employment_contract_personnel_custom_fields_pivot', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('value')->nullable()->comment('dữ liệu nhập pivot');
            $table->foreignId('employment_contract_personnel_id')->comment('khóa ngoại nhân sự')->constrained('employment_contract_personnels', 'id', 'fk_employment_contract_personnels')->cascadeOnDelete()->cascadeOnUpdate();
            $table
                ->foreignId('employment_contract_personnel_custom_field_id')
                ->comment('khóa ngoại cột thông tin nhân sự tự cấu hình')
                ->constrained('employment_contract_personnel_custom_fields', 'id', 'fk_employment_contract_personnel_custom_field')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_contract_personnel_pivot_employment_contract_personnel_custom_fields');
    }
};
