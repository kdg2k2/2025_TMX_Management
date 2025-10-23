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
        Schema::create('personnel_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('personnel_id')->comment('khóa ngoại nhân sự')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('type_id')->comment('khóa ngoại loại file')->constrained('personnel_file_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('path')->nullable()->comment('đường dẫn lưu file');
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnel_files');
    }
};
