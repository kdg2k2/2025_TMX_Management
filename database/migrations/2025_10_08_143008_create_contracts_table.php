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
        // hợp đồng
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name');
            $table->string('short_name');
            $table->integer('year');
            $table->string('contract_number')->comment('số hợp đồng');

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('Người tạo hợp đồng');

            $table->foreignId('instructor_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('người hướng dẫn');
            $table->foreignId('accounting_contact_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->comment('đầu mối kế toán');
            $table->foreignId('inspector_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Người kiểm tra sản phẩm trung gian của hợp đồng');
            $table->foreignId('executor_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Người thực hiện sản phẩm trung gian');

            $table->foreignId('type_id')->constrained('contract_types')->cascadeOnDelete()->cascadeOnUpdate()->comment('loại hợp đồng');
            $table->foreignId('investor_id')->nullable()->constrained('contract_investors')->cascadeOnDelete()->cascadeOnUpdate()->comment('nhà đầu tư');

            $table->double('contract_value')->nullable()->comment('giá trị hợp đồng');
            $table->double('vat_rate')->nullable()->comment('giá trị % thuế');
            $table->double('vat_amount')->nullable()->comment('tiền thuế');

            $table->date('signed_date')->nullable()->comment('Ngày ký hợp đồng');
            $table->date('effective_date')->nullable()->comment('Ngày hợp đồng có hiệu lực');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc hợp đồng');
            $table->date('completion_date')->nullable()->comment('Ngày hoàn thàn');
            $table->date('acceptance_date')->nullable()->comment('Ngày nghiệm thu hợp đồng');
            $table->date('liquidation_date')->nullable()->comment('Ngày thanh lý hợp đồng');

            $table->text('path_file_full')->nullable()->comment('đường dẫn file hợp đồng full - bản chính thức dã ký giữa 2 bên');
            $table->text('path_file_short')->nullable()->comment('đường dẫn file hợp đồng rút gọn - bản chính thức dã ký giữa 2 bên');

            $table->enum('contract_status', ['in_progress', 'completed'])->default('in_progress')->comment('Tình trạng hợp đồng');
            $table->enum('intermediate_product_status', [
                'completed',  // Đã hoàn thành
                'in_progress',  // Đang thực hiện
                'pending_review',  // Đề nghị kiểm tra
                'multi_year',  // Thực hiện nhiều năm
                'technical_done',  // Đã xong kỹ thuật
                'has_issues',  // Còn tồn tại
                'issues_recorded',  // Ghi nhận tồn tại
            ])->default('in_progress')->comment('tình trạng sản phẩm trung gian');
            $table->enum('financial_status', ['in_progress', 'completed'])->default('in_progress')->comment('tình trạng hồ sơ tài chính');

            $table->text('note')->nullable()->comment('ghi chú');

            $table->boolean('is_special')->default(false)->comment('Hợp đồng đặc biệt (true: hợp đồng đặc biệt, false: hợp đồng thường)');

            $table->string('a_side')->comment('bên a');
            $table->string('b_side')->comment('bên b');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
