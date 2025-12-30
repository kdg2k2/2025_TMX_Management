<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // sửa chữa thiết bị
        Schema::create('device_fixes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('device_id')->comment('thiết bị')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('created_by')->comment('người đăng ký')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('suggested_content')->comment('nội dung kiến nghị');
            $table->string('device_status')->comment('hiện trạng thiết bị');
            $table->foreignId('approved_by')->nullable()->comment('người phê duyệt')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('approval_note')->nullable()->comment('Ghi chú phê duyệt');
            $table->text('rejection_note')->nullable()->comment('Ghi chú từ chối phê duyệt');
            $table->timestamp('approved_at')->nullable()->comment('thời gian duyệt');
            $table->timestamp('fixed_at')->nullable()->comment('thời gian sửa xong');
            $table->integer('repair_costs')->nullable()->comment('kinh phí sửa chữa');
            $table->enum('status', [
                'pending',  // chờ phê duyệt
                'approved',  // đã duyệt
                'rejected',  // từ chối
                'fixed',  // đã sửa xong
            ])->default('pending');
            $table->string('device_status_upon_registration')->comment('trạng thái thiết bị khi đăng ký sửa chữa');
            $table->string('note')->comment('ghi chú')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_fixes');
    }
};
