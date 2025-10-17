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
        // thanh toán
        Schema::create('contract_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_finance_id')->comment('khóa ngoại tài chính')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('times')->default(1)->comment('lần thanh toán');
            $table->bigInteger('payment_amount')->comment('số tiền thanh toán');
            $table->bigInteger('invoice_amount')->comment('số tiền hóa đơn');
            $table->date('payment_date')->comment('ngày thanh toán');
            $table->date('invoice_date')->comment('ngày hóa đơn');
            $table->string('invoice_number')->comment('số hóa đơn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_payments');
    }
};
