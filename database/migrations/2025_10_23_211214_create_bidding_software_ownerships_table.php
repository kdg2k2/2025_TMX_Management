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
        // sở hữu phần mềm - đấu thầu
        Schema::create('bidding_software_ownerships', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('người tạo-cập nhật')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('bidding_id')->comment('khóa ngoại gói thầu')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('software_ownership_id')->comment('khóa sở hữu phần mềm')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['bidding_id', 'software_ownership_id'], 'bidding_software_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_software_ownerships');
    }
};
