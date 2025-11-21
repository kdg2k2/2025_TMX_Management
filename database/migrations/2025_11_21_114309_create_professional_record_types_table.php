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
        Schema::create('professional_record_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique()->comment('tên loại biên bản');
            $table->string('unit')->comment('đơn vị tính');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(0)->comment('số lượng');
            $table->integer('quantity_limit')->default(0)->comment('số lượng tối thiểu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_record_types');
    }
};
