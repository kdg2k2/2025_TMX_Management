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
        Schema::create('contract_scan_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('type_id')->constrained('contract_scan_file_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('path')->comment('đường dẫn lưu file');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('người tạo-cập nhật');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_scan_files');
    }
};
