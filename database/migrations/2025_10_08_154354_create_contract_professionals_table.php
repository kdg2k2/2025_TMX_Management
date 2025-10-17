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
        // phụ trách chuyên môn
        Schema::create('contract_professionals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('khóa ngoại tài khoản')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_professionals');
    }
};
