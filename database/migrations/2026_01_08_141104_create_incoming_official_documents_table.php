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
        // văn bản đến
        Schema::create('incoming_official_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('official_document_type_id')->comment('loại văn bản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('program_type', ['contract', 'orther'])->comment('loại chương trình');
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program_name')->nullable()->comment('tên chương trình khác');
            $table->string('document_number')->unique()->comment('số, ký hiệu văn bản');
            $table->date('issuing_date')->nullable()->comment('ngày phát hành');
            $table->date('received_date')->comment('ngày đến');
            $table->string('content_summary')->comment('trích yêu nội dung');
            $table->string('sender_address')->nullable()->comment('nơi gửi');
            $table->string('signer_name')->comment('họ tên người ký');
            $table->string('signer_position')->comment('chức danh người ký');
            $table->string('contact_person_name')->nullable()->comment('họ tên người liên hệ');
            $table->string('contact_person_address')->nullable()->comment('địa chỉ người liên hệ');
            $table->string('contact_person_phone')->nullable()->comment('số điện thoại người liên hệ');
            $table->string('notes')->nullable()->comment('ghi chú');
            $table->string('attachment_file')->comment('file đính kèm');
            $table->foreignId('task_assignee_id')->nullable()->comment('người thực hiện nhiệm vụ')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('task_completion_deadline')->nullable()->comment('hạn hoàn thành nhiệm vụ');
            $table->string('task_notes')->nullable()->comment('ghi chú nhiệm vụ');
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new')->comment('trạng thái xử lý văn bản');
            $table->timestamp('assign_at')->nullable()->comment('thời gian giao nhiệm vụ');
            $table->foreignId('assinged_by')->nullable()->comment('người giao nhiệm vụ')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamp('complete_at')->nullable()->comment('thời gian hoàn thành');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_official_documents');
    }
};
