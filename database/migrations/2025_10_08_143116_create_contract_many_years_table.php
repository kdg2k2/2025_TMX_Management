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
        // hợp đồng nhiều năm
        Schema::create('contract_many_years', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_many_years');
    }
};
