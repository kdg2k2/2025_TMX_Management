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
        Schema::create('bidding_implementation_personnel_files', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Khóa ngoại tới bảng bidding_implementation_personnels
            $table
                ->foreignId('bidding_implementation_personnel_id')
                ->comment('khóa ngoại nhân sự thực hiện tham gia gói thầu')
                ->constrained('bidding_implementation_personnels', 'id', 'fk_bidding_impl_personnel')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Khóa ngoại tới bảng personnel_files
            $table
                ->foreignId('personnel_file_id')
                ->comment('khóa ngoại file bằng cấp chứng chỉ của nhân sự')
                ->constrained('personnel_files', 'id', 'fk_personnel_file')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bidding_implementation_personnel_files');
    }
};
