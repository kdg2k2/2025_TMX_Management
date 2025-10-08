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
        // các loại đuôi file
        Schema::create('file_extensions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table
                ->string('extension')
                ->unique()
                ->comment('Phần mở rộng file, ví dụ: pdf, docx, xlsx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_extensions');
    }
};
