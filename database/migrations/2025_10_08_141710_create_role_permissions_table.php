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
        // trung gian quyền truy cập và chức năng
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['role_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
