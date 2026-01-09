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
        // email lĩnh vực
        Schema::create('official_document_sector_emails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('official_document_sector_id')->constrained('official_document_sectors', 'id', 'fk_odse_ods')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id', 'fk_odse_u')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['official_document_sector_id', 'user_id'], 'unique_odse_u');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_document_sector_emails');
    }
};
