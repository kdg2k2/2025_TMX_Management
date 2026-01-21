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
            <table></table>
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
    <x-approve-modal id="approve-modal" title="Xác nhận duyệt đăng ký" size="md" method="post"
        noteName="approval_note"></x-approve-modal>
    <x-approve-modal id="reject-modal" title="Xác nhận từ chối đăng ký" size="md" method="post"
        noteName="rejection_note" buttonVariant="danger"></x-approve-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.product.list'));

        const apiContractProductMainList = @json(route('api.contract.product.main.list'));
        const apiContractProductMainExport = @json(route('api.contract.product.main.export'));
        const apiContractProductMainImport = @json(route('api.contract.product.main.import'));

        const apiContractProductIntermediateList = @json(route('api.contract.product.intermediate.list'));
        const apiContractProductIntermediateExport = @json(route('api.contract.product.intermediate.export'));
        const apiContractProductIntermediateImport = @json(route('api.contract.product.intermediate.import'));

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
