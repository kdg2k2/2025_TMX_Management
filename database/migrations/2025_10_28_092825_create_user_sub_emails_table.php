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
        Schema::create('user_sub_emails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->comment('khóa ngoại tài khoản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('email')->comment('email');
            $table->unique(['user_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sub_emails');
    }
};
