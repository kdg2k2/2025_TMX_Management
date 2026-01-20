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
        // các năm kiểm tra - dành cho hợp đông nhiều năm
        Schema::create('contract_product_inspection_years', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_product_inspection_id')->constrained('contract_product_inspections', 'id', 'fk_cpiy_cpi');
            $table->year('year')->comment('năm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product_inspection_years');
    }
};
