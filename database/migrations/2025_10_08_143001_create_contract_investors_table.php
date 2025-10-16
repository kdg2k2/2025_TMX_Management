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
        // chủ đầu tư của hợp đồng
        Schema::create('contract_investors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name_vi')->unique()->comment('tên tiếng việt');
            $table->string('name_en')->unique()->comment('tên tiếng anh');
            $table->string('address')->nullable()->comment('địa chỉ');

            $table->unique(['name_vi', 'name_en'], 'unique_not_deleted')->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_investors');
    }
};
