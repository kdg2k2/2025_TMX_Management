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
        // yêu cầu kiểm tra sản phẩm
        Schema::create('contract_product_inspections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người yêu cầu kiểm tra')->constrained('users', 'id', 'fk_cpi_cb')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('supported_by')->comment('người hỗ trợ kiểm tra')->constrained('users', 'id', 'fk_cpi_sb')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', [
                'request',  // yêu cầu kiểm tra
                'responded',  // đã phản hồi
                'cancel', // hủy kiểm tra
            ])->default('request')->comment('trạng thái kiểm tra');
            $table->string('issue_file_path')->nullable()->comment('đường dẫn file danh sách vấn đề tồn tại');
            $table->string('note')->nullable()->comment('ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product_inspections');
    }
};
