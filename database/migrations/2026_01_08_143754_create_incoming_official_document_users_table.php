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
        // các thành viên tham gia hỗ trợ nhiệm vụ của văn bản đến
        Schema::create('incoming_official_document_users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('incoming_official_document_id')->constrained('incoming_official_documents','id','fk_iod')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users','id', 'fk_user')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['incoming_official_document_id', 'user_id'], 'iod_user_unq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_official_document_users');
    }
};
