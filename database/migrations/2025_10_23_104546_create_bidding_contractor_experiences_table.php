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
        // kinh nghiệm nhà thầu - đấu thầu
        Schema::create('bidding_contractor_experiences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bidding_id')->comment('khóa ngoại gói thầu')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_id')->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('file_type', ['path_file_full', 'path_file_short'])->default('path_file_full')->comment('bản hợp đồng');
            $table->unique(['bidding_id', 'contract_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_contractor_experiences');
    }
};
