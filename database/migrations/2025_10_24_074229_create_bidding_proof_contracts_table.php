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
        // hợp đồng minh chứng - đấu thầu
        Schema::create('bidding_proof_contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bidding_id')->comment('khóa ngoại gói thầu')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('proof_contract_id')->comment('khóa ngoại hợp đồng minh chứng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['bidding_id', 'proof_contract_id'], 'bidding_proof_contract_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_proof_contracts');
    }
};
