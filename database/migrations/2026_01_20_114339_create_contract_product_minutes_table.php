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
        // biên bản sptg
        Schema::create('contract_product_minutes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained('contracts', 'id', 'fk_cpm_c')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', [
                'draft',  // nháp
                'request_sign',  // yêu cầu ký
                'request_approve', // yêu cầu duyệt
                'approved', // đã duyệt
                'rejected', // từ chối
            ])->default('draft')->comment('trạng thái biên bản');
            $table->string('file_path')->comment('đường dẫn file biên bản');
            $table->string('issue_note')->nullable()->comment('ghi chú tồn tại (nếu có)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_product_minutes');
    }
};
