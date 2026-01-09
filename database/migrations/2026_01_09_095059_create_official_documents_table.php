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
        // công văn quyết định
        Schema::create('official_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người đề nghị')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('creater_position')->comment('vị trí người tạo')->constrained('positions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('official_document_type_id')->comment('loại văn bản')->constrained('official_document_types', 'id', 'fk_od_odt')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('official_document_sector_id')->comment('lĩnh vực')->nullable()->constrained('official_document_sectors', 'id', 'fk_od_ods')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('program_type', [
                'contract',
                'incoming',
                'orther',
            ])->comment('loại chương trình');
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('incoming_official_document_id')->nullable()->constrained('incoming_official_documents', 'id', 'fk_od_iod')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program_name')->nullable()->comment('tên chương trình khác');
            $table->enum('release_type', [
                'new',  // phát hành mới
                'revision',  // phát hành lại
                'reply',  // công văn trả lời
            ])->comment('Kiểu phát hành');
            $table->enum('status', [
                'pending_review',  // chờ kiểm tra
                'reviewed',  // đã kiểm tra
                'approved',  // đã duyệt
                'rejected',  // bị từ chối
                'released',  // đã phát hành
            ])->default('pending_review')->comment('trạng thái xử lý văn bản');
            $table->timestamp('approved_at')->nullable()->comment('thời gian duyệt');
            $table->foreignId('approved_by')->nullable()->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->string('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
            $table->string('name')->comment('tên văn bản');
            $table->foreignId('reviewed_by')->nullable()->comment('người kiểm tra')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('signed_by')->comment('người ký')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('expected_release_date')->comment('ngày dự kiến phát hành');
            $table->string('receiver_organization')->comment('nơi nhận');
            $table->string('receiver_name')->comment('họ tên người nhận trực tiếp');
            $table->string('receiver_address')->comment('địa chỉ nơi nhận');
            $table->string('receiver_phone')->comment('điện thoại liên hệ nơi nhận');
            $table->string('note')->comment('ghi chú');
            $table->string('pending_review_docx_file')->comment('file docx soạn thảo');
            $table->string('revision_docx_file')->nullable()->comment('file docx điều chỉnh');
            $table->string('comment_docx_file')->nullable()->comment('file docx nhận xét');
            $table->string('approve_docx_file')->nullable()->comment('file docx phê duyệt');
            $table->string('released_pdf_file')->nullable()->comment('file pdf phát hành');
            $table->string('document_number')->nullable()->comment('số, ký hiệu văn bản phát hành');
            $table->date('released_date')->nullable()->comment('ngày phát hành');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_documents');
    }
};
