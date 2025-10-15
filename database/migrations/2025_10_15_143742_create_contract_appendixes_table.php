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
        Schema::create('contract_appendixes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('content')->nullable()->comment('nội dung');
            $table->date('renewal_date')->comment('ngày gia hạn');
            $table->date('renewal_end_date')->comment('ngày gia hạn');
            $table->string('renewal_letter')->nullable()->comment('Công văn gia hạn');
            $table->string('renewal_approval_letter')->nullable()->comment('Công văn đồng ý gia hạn');
            $table->string('renewal_appendix')->nullable()->comment('Phụ lục gia hạn');
            $table->string('other_documents')->nullable()->comment('Hồ sơ khác');
            $table->string('adjusted_value')->nullable()->comment('Giá trị điều chỉnh');
            $table->string('note')->nullable()->comment('Ghi chú');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_appendixes');
    }
};
