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
        // các định dạng cho loại file hợp đồng
        Schema::create('contract_file_type_extensions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('type_id')->constrained('contract_file_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('extension_id')->constrained('file_extensions')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_file_type_extensions');
    }
};
