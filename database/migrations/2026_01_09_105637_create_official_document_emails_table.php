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
        Schema::create('official_document_emails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('official_document_id')->constrained('official_documents', 'id', 'fk_ode_od')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users', 'id', 'fk_ode_u')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['official_document_id', 'user_id'], 'unique_ode_u');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_document_emails');
    }
};
