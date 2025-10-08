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
        // địa điểm của hợp đồng
        Schema::create('contract_scopes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('province_c');
            $table->foreign('province_c')->references('code')->on('provinces')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique([
                'contract_id',
                'province_c',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_scopes');
    }
};
