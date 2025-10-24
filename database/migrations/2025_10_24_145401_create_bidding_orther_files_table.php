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
        // các file khác - đấu thầu
        Schema::create('bidding_orther_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bidding_id')->comment('khóa ngoại gói thầu')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('content')->comment('nội dung file');
            $table->string('path')->comment('đường dẫn lưu file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_orther_files');
    }
};
