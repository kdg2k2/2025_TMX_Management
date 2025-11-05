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
        // chi tiết kế hoạch hồ sơ ngoại nghiệp
        Schema::create('dossier_plan_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dossier_plan_id')->comment('khóa ngoại kế hoạch')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('dossier_type_id')->comment('khóa ngoại loại giấy tờ')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('province_code')->comment('mã tỉnh');
            $table->foreign('province_code')->references('code')->on('provinces')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('commune_code')->nullable()->comment('mã xã');
            $table->foreign('commune_code')->references('code')->on('communes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('unit_id')->nullable()->comment('khóa ngoại ')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('estimated_time')->nullable()->comment('thời gian');
            $table->foreignId('responsible_user_id')->comment('khóa ngoại người phụ trách')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(0)->comment('số lượng');
            $table->text('note')->nullable()->comment('ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_plan_details');
    }
};
