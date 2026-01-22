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
            $table->foreignId('created_by')->comment('người yêu cầu biên bản')->constrained('users', 'id', 'fk_cpm_cb')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_id')->constrained('contracts', 'id', 'fk_cpm_c')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('status', [
                'draft',  // nháp
                'request_sign',  // yêu cầu ký
                'request_approve',  // yêu cầu duyệt
                'approved',  // đã duyệt
                'rejected',  // từ chối
            ])->default('draft')->comment('trạng thái biên bản');
            $table->date('handover_date')->nullable()->comment('Ngày giao');
            $table->text('legal_basis')->nullable()->comment('Căn cứ (vào điều A,B...)');
            $table->text('handover_content')->nullable()->comment('nội dung bàn giao');
            $table->foreignId('contract_professional_id')->comment('phụ trách chuyên môn')->constrained('contract_professionals', 'id', 'fk_cpm_cp')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_disbursement_id')->comment('phụ trách giải ngân')->constrained('contract_disbursements', 'id', 'fk_cpm_cd')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('file_docx_path')->nullable()->comment('đường dẫn raw file biên bản');
            $table->string('file_pdf_path')->nullable()->comment('đường dẫn scan file biên bản đã full chữ ký');
            $table->string('issue_note')->nullable()->comment('ghi chú tồn tại (nếu có)');
            $table->timestamp('approved_at')->nullable()->comment('thời gian duyệt');
            $table->foreignId('approved_by')->nullable()->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->text('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
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
