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
        // nhân sự hợp đồng
        Schema::create('contract_personnels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_id')->comment('hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('personnel_id')->comment('nhân sự')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_in_contract')->default(true)->comment('Có trong hợp đồng (có/không)');
            $table->string('position')->nullable()->comment('Chức danh');
            $table->string('position_en')->nullable()->comment('Chức danh (EN)');
            $table->string('mobilized_unit')->nullable()->comment('Đơn vị huy động');
            $table->string('mobilized_unit_en')->nullable()->comment('Đơn vị huy động (EN)');
            $table->string('task')->nullable()->comment('Nhiệm vụ thực hiện trong hợp đồng');
            $table->string('task_en')->nullable()->comment('Nhiệm vụ thực hiện trong hợp đồng (EN)');

            $table->unique(['contract_id', 'personnel_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_personnels');
    }
};
