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
        // người dùng
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
            $table->string('name')->comment('tên');
            $table->string('email')->unique()->comment('email');
            $table->string('password')->comment('mật khẩu');
            $table->string('phone')->nullable()->unique()->comment('số điện thoại');
            $table->string('citizen_identification_number')->nullable()->comment('số căn cước công dân');
            $table->text('path')->nullable()->comment('ảnh đại diện');
            $table->text('path_signature')->nullable()->comment('ảnh chữ ký -  đùng cho chức năng ký số');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->comment('phòng ban');
            $table->foreignId('position_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->comment('chức vụ');
            $table->foreignId('job_title_id')->constrained()->cascadeOnDelete()->cascadeOnDelete()->comment('chức danh');
            $table->foreignId('role_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_banned')->default(false)->comment('khóa tài khoản');
            $table->boolean('retired')->default(false)->comment('nghỉ việc');
            $table->integer('jwt_version')->default(1)->comment('phiên bản token đăng nhập - dựa vào đây để ép logout user mobile');
            $table->boolean('payroll')->default(true)->comment('tính lương');

            $table->unique(['email', 'phone'], 'unique_not_deleted')->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
