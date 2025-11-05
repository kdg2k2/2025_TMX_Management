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
        // bàn giao hồ sơ ngoại nghiệp
        Schema::create('dossier_handovers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', ['out', 'in'])->comment('kiểu bản giao - out = bàn giao đi, in = bàn giao về');
            $table->foreignId('dossier_plan_id')->comment('khóa ngoại kế hoạch')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->comment('khóa ngoại người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('handover_by')->comment('khóa ngoại người giao')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('received_by')->comment('khóa ngoại người nhận')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('times')->default(1)->comment('lần bàn giao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_handovers');
    }
};
