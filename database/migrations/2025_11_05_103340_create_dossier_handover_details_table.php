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
        // chi tiết bàn giao hồ sơ ngoại nghiệp
        Schema::create('dossier_handover_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dossier_handover_id')->comment('khóa ngoại bàn giao')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('dossier_type_id')->comment('khóa ngoại loại giấy tờ')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('province_code')->comment('mã tỉnh');
            $table->foreign('province_code')->references('code')->on('provinces')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('commune_code')->nullable()->comment('mã xã');
            $table->foreign('commune_code')->references('code')->on('communes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('unit_id')->nullable()->comment('khóa ngoại đơn vị')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(0)->comment('số lượng');
            $table->text('note')->nullable()->comment('ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossier_handover_details');
    }
};
