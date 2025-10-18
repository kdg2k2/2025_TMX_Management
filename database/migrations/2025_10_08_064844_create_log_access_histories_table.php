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
        // lưu log truy cập hệ thống
        Schema::create('log_access_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->comment('khóa ngoại tài khoản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('url');
            $table->string('method');
            $table->json('body')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_access_histories');
    }
};
