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
            $table->softDeletes();
            $table->text('name')->comment('tên hợp đồng');
            $table->string('short_name')->unique()->comment('tên viết tắt');
            $table->integer('year')->comment('năm');
            $table->string('contract_number')->unique()->comment('số hợp đồng');

            $table->text('name_en')->nullable()->comment('tên hợp đồng (tiếng anh)');
            $table->text('target_vi')->nullable()->comment('Mục tiêu (tiếng việt)');
            $table->text('target_en')->nullable()->comment('Mục tiêu (tiếng anh)');
            $table->text('main_activities_vi')->nullable()->comment('Hoạt động chinh (tiếng việt)');
            $table->text('main_activities_en')->nullable()->comment('Hoạt động chinh (tiếng anh)');
            $table->text('product_vi')->nullable()->comment('Sản phẩm (tiếng việt)');
            $table->text('product_en')->nullable()->comment('Sản phẩm (tiếng anh)');

            $table->foreignId('created_by')->comment('Người tạo hợp đồng')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('accounting_contact_id')->comment('đầu mối kế toán')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('inspector_user_id')->comment('Người kiểm tra sản phẩm trung gian của hợp đồng')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('executor_user_id')->comment('Người thực hiện sản phẩm trung gian')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('type_id')->comment('loại hợp đồng')->constrained('contract_types')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('investor_id')->comment('nhà đầu tư')->nullable()->constrained('contract_investors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('vi_name_of_investor_reference_person')->nullable()->comment('Tên tiếng việt - người tham chiếu của nhà đầu tư');
            $table->string('en_name_of_investor_reference_person')->nullable()->comment('Tên tiếng anh - người tham chiếu của nhà đầu tư');

            $table->bigInteger('contract_value')->nullable()->comment('giá trị hợp đồng');
            $table->double('vat_rate')->nullable()->comment('giá trị % thuế');
            $table->bigInteger('vat_amount')->nullable()->comment('tiền thuế');

            $table->date('signed_date')->nullable()->comment('Ngày ký hợp đồng');
            $table->date('effective_date')->nullable()->comment('Ngày hợp đồng có hiệu lực');
            $table->date('end_date')->nullable()->comment('Ngày kết thúc hợp đồng');
            $table->date('completion_date')->nullable()->comment('Ngày hoàn thành');
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

            $table->unique(['short_name', 'contract_number'], 'unique_not_deleted')->whereNull('deleted_at');
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
