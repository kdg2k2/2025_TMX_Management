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
        // Biên bản hội đồng quản trị
        Schema::create('board_meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('created_by')->comment('khóa ngoại người tạo')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('number')->comment('biên bản số');
            $table->date('meeting_day')->comment('ngày họp');
            $table->text('main_content')->comment('nội dung chính');
            $table->string('path')->nullable()->comment('đường dân file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_meeting_minutes');
    }
};
