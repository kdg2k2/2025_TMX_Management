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
        // chi tiết vé tàu xe
        Schema::create('train_and_bus_ticket_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('train_and_bus_ticket_id')->constrained('train_and_bus_tickets')->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('user_type', ['internal', 'external'])->comment('kiểu người đăng ký');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('external_user_name')->nullable()->comment('tên người ngoài');
            $table->date('departure_date')->nullable()->comment('ngày khởi hành');
            $table->date('return_date')->nullable()->comment('ngày về');
            $table->string('departure_place')->nullable()->comment('nơi khởi hành');
            $table->string('return_place')->nullable()->comment('nơi về');
            $table->string('train_number')->nullable()->comment('số hiệu tàu xe');
            $table->integer('ticket_price')->nullable()->comment('giá vé');
            $table->string('ticket_image_path')->nullable()->comment('ảnh vé');
            $table->string('note')->nullable()->comment('ghi chú');
            $table->foreignId('created_by')->comment('người tạo')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('train_and_bus_ticket_details');
    }
};
