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
        // nhân sự thực hiện - đấu thầu
        Schema::create('bidding_implementation_personnels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bidding_id')->comment('khóa ngoại gói thầu')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('personnel_id')->comment('khóa ngoại nhân sự')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('job_title', [
                'project_manager',  // Chủ nhiệm dự án
                'topic_leader',  // Chủ trì chuyên đề
                'expert',  // Chuyên gia
                'support_staff',  // Cán bộ hỗ trợ
            ])->comment('chức danh');
            $table->unique(['bidding_id', 'personnel_id'], 'bidding_personnel_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_implementation_personnels');
    }
};
