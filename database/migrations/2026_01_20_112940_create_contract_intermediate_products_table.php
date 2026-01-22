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
        // sản phẩm trung gian
        Schema::create('contract_intermediate_products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->year('year')->comment('năm');
            $table->string('contract_number')->comment('số hợp đồng');
            $table->string('name')->comment('tên sản phẩm');
            $table->string('executor_user_name')->comment('tên người thực hiện');
            $table->string('note')->nullable()->comment('ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_intermediate_products');
    }
};
