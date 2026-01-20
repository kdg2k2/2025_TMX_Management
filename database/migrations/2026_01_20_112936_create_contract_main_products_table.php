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
        // sản phẩm chính
        Schema::create('contract_main_products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->year('year')->comment('năm');
            $table->string('name')->comment('tên sản phẩm');
            $table->integer('quantity')->comment('số lượng');
            $table->string('note')->nullable()->comment('ghi chú');
            $table->string('comment')->nullable()->comment('nhận xét');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_main_products');
    }
};
