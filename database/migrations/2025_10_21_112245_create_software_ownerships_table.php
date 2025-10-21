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
        // sở hữu phần mềm
        Schema::create('software_ownerships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('khóa ngoại người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name')->unique()->comment('tên tài liệu');
            $table->string('path')->nullable()->comment('đường dẫn lưu file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_ownerships');
    }
};
