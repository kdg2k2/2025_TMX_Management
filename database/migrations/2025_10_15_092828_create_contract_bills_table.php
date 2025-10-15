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
        Schema::create('contract_bills', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bill_collector')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('người phụ trách lấy');
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('path')->nullable();
            $table->float('amount')->comment('số tiền');
            $table->date('duration')->comment('thời hạn');
            $table->text('content_in_the_estimate')->comment('Nội dung trong dự toán');
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_bills');
    }
};
