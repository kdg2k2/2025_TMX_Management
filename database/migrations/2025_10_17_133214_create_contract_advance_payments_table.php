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
        // tạm ứng
        Schema::create('contract_advance_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_finance_id')->comment('khóa ngoại tài chính')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('times')->default(1)->comment('lần tạm ứng');
            $table->bigInteger('amount')->comment('số tiền tạm ứng');
            $table->date('date')->comment('ngày tạm ứng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_advance_payments');
    }
};
