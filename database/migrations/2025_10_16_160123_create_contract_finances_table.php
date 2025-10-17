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
        // tài chính
        Schema::create('contract_finances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contract_id')->comment('khóa ngoại hợp đồng')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_unit_id')->comment('khóa ngoại đơn vị liên danh')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('role', [
                'head_of_the_joint_venture',  // Đứng đầu liên danh
                'joint_venture_members',  // Thành viên liên danh
                'subcontractors',  // Thầu phụ
            ])->comment('vai trò');
            $table->bigInteger('realized_value')->comment('giá trị thực hiện');
            $table->bigInteger('acceptance_value')->comment('giá trị nghiệm thu');
            $table->double('vat_rate')->comment('giá trị % thuế');
            $table->bigInteger('vat_amount')->comment('tiền thuế');

            $table->unique(['contract_id', 'contract_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_finances');
    }
};
