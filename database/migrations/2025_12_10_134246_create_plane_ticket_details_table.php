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
        // chi tiết vé máy bay
        Schema::create('plane_ticket_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('plane_ticket_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->enum('user_type', ['internal', 'external'])->comment('kiểu người đăng ký');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('external_user_name')->nullable()->comment('tên người ngoài');
            $table->date('departure_date')->nullable()->comment('ngày khởi hành');
            $table->date('return_date')->nullable()->comment('ngày về');
            $table->foreignId('departure_airport_id')->nullable()->comment('sân bay khởi hành')->constrained('airports')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('return_airport_id')->nullable()->comment('sân bay đến')->constrained('airports')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('airline_id')->nullable()->comment('hãng bay')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plane_ticket_class_id')->nullable()->comment('hạng vé')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('checked_baggage_allowances')->default(0)->comment('số cân hành lý ký gửi (kg)');
            $table->integer('ticket_price')->nullable()->comment('giá vé');
            $table->string('ticket_image_path')->nullable()->comment('ảnh vé');
            $table->string('note')->nullable()->comment('ghi chú');
            $table->foreignId('created_by')->comment('người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plane_ticket_details');
    }
};
