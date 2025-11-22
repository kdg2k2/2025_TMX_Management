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
        // vé tàu xe
        Schema::create('train_and_bus_tickets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('type', ['contract', 'other'])->comment('kiểu đăng ký');
            $table->foreignId('contract_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('other_program_name')->nullable()->comment('tên chương trình khác');
            $table->date('estimated_travel_time')->comment('thời gian đi dự kiến');
            $table->string('expected_departure')->comment('điểm khởi hành dự kiến');
            $table->string('expected_destination')->comment('điểm đến hành dự kiến');
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('train_and_bus_tickets');
    }
};
