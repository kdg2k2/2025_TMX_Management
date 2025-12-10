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
        Schema::table('task_schedules', function (Blueprint $table) {
            $table->string('cron_expression')->nullable()->comment('biểu thức cron')->change();
            $table->boolean('manual_run')->default(false)->comment('Cho phép chạy thủ công');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_schedules', function (Blueprint $table) {
            $table->string('cron_expression')->nullable(false)->comment('biểu thức cron')->change();
            $table->dropColumn(['manual_run']);
        });
    }
};
