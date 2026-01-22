@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Hợp đồng', 'url' => null],
        ['label' => 'Sản phẩm', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 my-1">
            <label>
                Trạng thái biên bản
            </label>
            <select name="contract_product_minute_status" id="minute-status" class="form-control">
                <x-select-options :items="$minuteStatuses" keyField="original" valueFields="converted" :optionCallback="fn($item) => isset($item['color']) ? 'class=text-' . $item['color'] : ''" />
            </select>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="product-modal" size="lg" nested="true">
        <x-slot:body>
            <table class="display w-100"></table>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        </x-slot:footer>
    </x-modal>
    <x-modal id="import-product-modal" method="put" size="sm" nested="true">
        <x-slot:body>
            <div class="contract-year-filter-container my-1"></div>
            <div class="my-1">
                <label>
                    Chọn file (.xlsx)
                </label>
                <input type="file" class="form-control" accept=".xlsx" name="file" required>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="inspection-product-modal" size="xl" nested="true">
        <x-slot:body>
            <table class="display w-100"></table>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        </x-slot:footer>
    </x-modal>
    <x-modal id="request-inspection-product-modal" method="post" size="md" nested="true">
        <x-slot:body>
            <div class="contract-year-filter-container my-1"></div>
            <div class="my-1">
                <label>
                    Người hỗ trợ kiểm tra
                </label>
                <select name="supported_by" required>
                    <x-select-options :items="$users" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    Mô tả cần hỗ trợ
                </label>
                <input type="text" class="form-control" name="support_description" required>
            </div>
            <div class="my-1">
                <label>
                    File danh sách vấn đề cần hỗ trợ (docx,xlsx,rar,zip)
                </label>
                <input type="file" class="form-control" name="issue_file_path" accept=".docx,.xlsx,.rar,.zip">
            </div>
            <div class="my-1">
                <label>
                    Ghi chú
                </label>
                <input type="text" class="form-control" name="note">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
    <x-modal id="cancel-inspection-product-modal" method="patch" size="sm" nested="true">
        <x-slot:body>
            Chắc chắn hủy kiểm tra?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
    <x-modal id="response-inspection-product-modal" method="patch" size="md" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Nhận xét của người kiểm tra
                </label>
                <input type="text" class="form-control" name="inspector_comment" required>
            </div>
            <div class="my-1">
                <label>
                    File nhận xét (docx,xlsx,rar,zip)
                </label>
                <input type="file" class="form-control" name="inspector_comment_file_path"
                    accept=".docx,.xlsx,.rar,.zip">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng"
                data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="minute-product-modal" nested="true" :fullscreen="true">
        <x-slot:body>
            <div class="h-100 d-flex flex-column">
                <div class="row g-3 flex-fill">

                    <div class="col-6">
                        <div class="card m-0">
                            <div class="card-header">
                                <b>Danh sách biên bản</b>
                            </div>
                            <div class="card-body">
                                <table class="display w-100"></table>
                            </div>
                        </div>
                    </div>

                    <div class="col-6 d-flex">
                        <div class="card m-0 flex-fill d-flex flex-column">
                            <div class="card-header">
                                <b>Xem biên bản</b>
                            </div>
                            <div class="card-body p-0 flex-fill">
                                <iframe src="" class="w-100 h-100 bg-light" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-slot:body>
    </x-modal>
    <x-modal id="create-minute-product-modal" method="post" nested="true" size="md">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Phụ trách chuyên môn
                </label>
                <select name="contract_professional_id" id="contract-professional-id" required></select>
            </div>
            <div class="my-1">
                <label>
                    Phụ trách giải ngân
                </label>
                <select name="contract_disbursement_id" id="contract-disbursement-id" required></select>
            </div>
            <div class="my-1">
                <label>
                    Ngày giao
                </label>
                <input type="date" class="form-control" name="handover_date">
            </div>
            <div class="my-1">
                <label>
                    Căn cứ
                </label>
                <textarea name="legal_basis" class="form-control" rows="3"></textarea>
            </div>
            <div class="my-1">
                <label>
                    Nội dung bàn giao
                </label>
                <textarea name="handover_content" class="form-control" rows="3"></textarea>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng"
                data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-approve-modal id="approve-modal" title="Xác nhận duyệt đăng ký" size="md" method="post"
        noteName="approval_note"></x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối đăng ký" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger"></x-approve-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.product.list'));
        const apiContractManyYearList = @json(route('api.contract.many-year.list'));
        const apiContractProfessionalList = @json(route('api.contract.professional.list'));
        const apiContractDisbursementList = @json(route('api.contract.disbursement.list'));

        const apiContractProductMainList = @json(route('api.contract.product.main.list'));
        const apiContractProductMainExport = @json(route('api.contract.product.main.export'));
        const apiContractProductMainImport = @json(route('api.contract.product.main.import'));

        const apiContractProductIntermediateList = @json(route('api.contract.product.intermediate.list'));
        const apiContractProductIntermediateExport = @json(route('api.contract.product.intermediate.export'));
        const apiContractProductIntermediateImport = @json(route('api.contract.product.intermediate.import'));

        const apiContractProductInspectionList = @json(route('api.contract.product.inspection.list'));
        const apiContractProductInspectionRequest = @json(route('api.contract.product.inspection.request'));
        const apiContractProductInspectionCancel = @json(route('api.contract.product.inspection.cancel'));
        const apiContractProductInspectionResponse = @json(route('api.contract.product.inspection.response'));

        const contractProductMinuteApprove = @json(route('contract.product.minute.approve'));
        const contractProductMinuteReject = @json(route('contract.product.minute.reject'));

        const apiContractProductMinuteList = @json(route('api.contract.product.minute.list'));
        const apiContractProductMinuteCreate = @json(route('api.contract.product.minute.create'));
        const apiContractProductMinuteReplace = @json(route('api.contract.product.minute.replace'));
        const apiContractProductMinuteSignatureRequest = @json(route('api.contract.product.minute.signature-request'));
        const apiContractProductMinuteConfirmIssues = @json(route('api.contract.product.minute.confirm-issues'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/product/list.js?v={{ time() }}"></script>
    <script src="assets/js/contract/product/filter.js?v={{ time() }}"></script>
    <script src="assets/js/components/approve-reject-modal-event.js?v={{ time() }}"></script>
    <script src="assets/js/contract/product/modals.js?v={{ time() }}"></script>
@endsection
