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
        // gia hạn hợp đồng
        Schema::create('contract_extensions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('old_end_date')->comment('ngày kết thúc cũ');
            $table->date('new_end_date')->comment('ngày kết thúc mới');
            $table->text('extension_reason')->nullable()->comment('lý do gia hạn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_extensions');
    }
};
