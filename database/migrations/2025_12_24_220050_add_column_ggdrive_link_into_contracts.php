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
        // thêm cột lưu link drive cho hợp đồng
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('ggdrive_link')->nullable()->comment('link gg drive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['ggdrive_link']);
        });
    }
};
