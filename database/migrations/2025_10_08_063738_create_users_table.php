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
            $table->foreignId('department_id')->comment('phòng ban')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('position_id')->comment('chức vụ')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('job_title_id')->comment('chức danh')->constrained()->cascadeOnDelete()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->comment('quyền truy cập')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_banned')->default(false)->comment('khóa tài khoản');
            $table->boolean('is_retired')->default(false)->comment('nghỉ việc');
            $table->integer('jwt_version')->default(1)->comment('phiên bản token đăng nhập - dựa vào đây để ép logout user mobile');

            $table->boolean('is_salary_counted')->default(true)->comment('Tính lương');
            $table->boolean('is_permanent')->default(true)->comment('Cơ hữu');  // Nhân viên cơ hữu
            $table->boolean('is_childcare_mode')->default(false)->comment('Chế độ con nhỏ');
            $table->date('date_of_birth')->nullable()->comment('ngày sinh');
            $table->string('address')->nullable()->comment('địa chỉ');
            $table->integer('salary_level')->default(0)->comment('Mức lương');
            $table->integer('violation_penalty')->default(0)->comment('Mức tiêu chí (số tiền phạt khi vi phạm quy chế)');
            $table->integer('allowance_contact')->default(0)->comment('Phụ cấp liên lạc');
            $table->integer('allowance_position')->default(0)->comment('Phụ cấp chức vụ');
            $table->integer('allowance_fuel')->default(0)->comment('Phụ cấp xăng xe');
            $table->integer('allowance_transport')->default(0)->comment('Phụ cấp đi lại');
            $table->date('work_start_date')->nullable()->comment('Ngày bắt đầu đi làm');  // Để tính công, tính lương
            $table->date('work_end_date')->nullable()->comment('Ngày kết thúc đi làm');  // Để tính công, tính lương

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
