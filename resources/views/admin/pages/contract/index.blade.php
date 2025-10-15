@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Hợp đồng', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('contract.create')" />
    </x-breadcrumb>

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <table class="display w-100" id="datatable"></table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="contract-detail-modal" title="Chi tiết hợp đồng" size="xl">
        <x-slot:body>
            <div id="contract-detail-content">
                <x-nav-tab id="contract-detail-tab" style="pills" :tabs="[
                    [
                        'title' => 'Thông tin chung',
                        'icon' => 'ti ti-info-circle',
                        'content' => view('admin.pages.contract.partials.general-info')->render(),
                    ],
                    [
                        'title' => 'Tài liệu',
                        'icon' => 'ti ti-files',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'document-count'],
                        'content' => view('admin.pages.contract.partials.documents-info', [
                            'fileTypes' => $fileTypes,
                        ])->render(),
                    ],
                    [
                        'title' => 'Hóa đơn',
                        'icon' => 'ti ti-receipt-2',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'bill-count'],
                        'content' => view('admin.pages.contract.partials.bills-info', [
                            'users' => $users,
                        ])->render(),
                    ],
                ]" />
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
        </x-slot:footer>
    </x-modal>

    <x-modal id="create-contract-file-modal" title="Thêm mới file hợp đồng" size="md" nested="true"
        action="{{ route('api.contract.file.store') }}" method="post">
        <x-slot:body>
            <input name="contract_id" hidden>
            <div class="form-group my-1">
                <label>
                    Loại file
                </label>
                <select name="type_id" id="contract-file-type" required>
                    <x-select-options :items="$fileTypes" recordAttribute="data-record"></x-select-options>
                </select>
            </div>
            <div class="form-group my-1" hidden>
                <label id="contract-file-label"></label>
                <input class="form-control" type="" name="path" id="contract-file-input" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Nội dung cập nhật
                </label>
                <input class="form-control" type="text" name="updated_content">
            </div>
            <div class="form-group my-1">
                <label>
                    Ghi chú
                </label>
                <input class="form-control" type="text" name="note">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="contract-bill-modal" title="Hóa đơn" size="md" nested="true">
        <x-slot:body>
            <input name="contract_id" hidden>
            <div class="form-group my-1">
                <label>
                    Người phụ trách lấy
                </label>
                <select name="bill_collector" required>
                    <x-select-options :items="$users"></x-select-options>
                </select>
            </div>
            <div class="form-group my-1">
                <label>
                    Số tiền HĐ
                </label>
                <input class="form-control" type="text" name="amount" id="bill-amount" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Thời hạn
                </label>
                <input class="form-control" type="date" name="duration" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Nội dung dự toán
                </label>
                <input class="form-control" type="text" name="content_in_the_estimate" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Ghi chú
                </label>
                <input class="form-control" type="text" name="note">
            </div>
            <div class="form-group my-1">
                <label>
                    File hóa đơn (xls,xlsx,pdf,xml,jpg,rar,zip)
                </label>
                <input class="form-control" type="file" name="path" accept=".xls,.xlsx,.pdf,.xml,.jpg,.rar,.zip">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.contract.list'));
        const showUrl = @json(route('api.contract.show'));
        const editUrl = @json(route('contract.edit'));
        const deleteUrl = @json(route('contract.delete'));

        const listFileUrl = @json(route('api.contract.file.list'));
        const viewFileUrl = @json(route('api.contract.file.view-file'));
        const deleteFileUrl = @json(route('contract.file.delete'));

        const listBillUrl = @json(route('api.contract.bill.list'));
        const storeBillUrl = @json(route('api.contract.bill.store'));
        const updateBillUrl = @json(route('api.contract.bill.update'));
        const deleteBillUrl = @json(route('contract.bill.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/contract/list.js"></script>
    <script src="assets/js/contract/detail-modal-content/script.js"></script>
    <script src="assets/js/contract/detail-modal-content/general-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/documents-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/bills-info.js"></script>
@endsection
