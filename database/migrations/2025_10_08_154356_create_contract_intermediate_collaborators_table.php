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
        // phối hợp làm sptg
        Schema::create('contract_intermediate_collaborators', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->comment('khóa ngoại hợp đồng');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_intermediate_collaborators');
    }
};
