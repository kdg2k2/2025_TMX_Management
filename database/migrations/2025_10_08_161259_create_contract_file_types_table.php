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
        // các loại file cho hợp đồng
        Schema::create('contract_file_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->comment('tên loại file hợp đồng');
            $table->string('description')->nullable()->comment('mô tả');
            $table->enum('type', ['file', 'url'])->comment('loại file');  // lưu file hay url
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_file_types');
    }
};
