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
        // các mã kaspersky được bàn giao khi duyệt đăng ký
        Schema::create('kaspersky_code_registration_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('kaspersky_code_registration_id')->comment('phiếu đăng ký')->constrained('kaspersky_code_registrations', 'id', 'fk_kcri_kcr')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('kaspersky_code_id')->comment('mã kaspersky được bàn giao')->constrained('kaspersky_codes', 'id', 'fk_kcri_kc')->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique([
                'kaspersky_code_registration_id',
                'kaspersky_code_id'
            ], 'unique_kcr_kc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaspersky_code_registration_items');
    }
};
