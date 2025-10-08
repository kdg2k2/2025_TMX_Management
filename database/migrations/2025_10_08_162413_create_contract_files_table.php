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
        // các file của hợp đồng
        Schema::create('contract_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('type_id')->constrained('contract_file_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('path');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('updated_content')->nullable()->comment('nội dung cập nhật');
            $table->string('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_files');
    }
};
