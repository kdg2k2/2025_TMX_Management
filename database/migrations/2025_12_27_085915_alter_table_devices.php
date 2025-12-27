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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['previous_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->enum('previous_status', [
                'normal',  // bình thường
                'broken',  // hỏng
                'faulty',  // lỗi
                'lost',  // thất lạc
                'loaned',  // cho mượn
                'under_repair',  // sửa chữa
                'stored',  // lưu kho
            ])->nullable()->comment('tình trạng trước đó');
        });
    }
};
