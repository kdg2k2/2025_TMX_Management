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
        Schema::create('professional_record_plans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('handover_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('received_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('handover_date')->comment('ngày bàn giao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_plans');
    }
};
