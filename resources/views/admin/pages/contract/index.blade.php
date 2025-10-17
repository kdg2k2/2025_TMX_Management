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
                        'onclick' => 'loadContractDetail()',
                    ],
                    [
                        'class' => 'border-0',
                        'title' => 'Tài liệu',
                        'icon' => 'ti ti-files',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'document-count'],
                        'content' => view('admin.pages.contract.partials.documents-info', [
                            'fileTypes' => $fileTypes,
                        ])->render(),
                        'onclick' => 'renderDocumentsInfo()',
                    ],
                    [
                        'class' => 'border-0',
                        'title' => 'Hóa đơn',
                        'icon' => 'ti ti-receipt-2',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'bill-count'],
                        'content' => view('admin.pages.contract.partials.bills-info', [
                            'users' => $users,
                        ])->render(),
                        'onclick' => 'renderBillsInfo()',
                    ],
                    [
                        'class' => 'border-0',
                        'title' => 'Phụ lục hợp đồng',
                        'icon' => 'ti ti-file-plus',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'appendix-count'],
                        'content' => view('admin.pages.contract.partials.appendixes-info', [
                            'users' => $users,
                        ])->render(),
                        'onclick' => 'renderAppendixesInfo()',
                    ],
                    [
                        'class' => 'border-0',
                        'title' => 'Tài chính',
                        'icon' => 'ti ti-brand-cashapp',
                        'badge' => ['text' => '', 'color' => 'info', 'id' => 'finance-count'],
                        'content' => view('admin.pages.contract.partials.finances-info', [
                            'users' => $users,
                        ])->render(),
                        'onclick' => 'renderFinancesInfo()',
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
                    Số tiền HĐ(vnđ)
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

    <x-modal id="contract-appendix-modal" title="Phụ lục hợp đồng" size="md" nested="true">
        <x-slot:body>
            <input name="contract_id" hidden>
            <div class="form-group my-1">
                <label>
                    Nội dung
                </label>
                <input class="form-control" type="text" name="content" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Ngày gia hạn
                </label>
                <input class="form-control" type="date" name="renewal_date" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Ngày kết thúc gia hạn
                </label>
                <input class="form-control" type="date" name="renewal_end_date" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Công văn gia hạn (doc,docx,pdf,rar,zip)
                </label>
                <input class="form-control" type="file" name="renewal_letter" accept=".doc,.docx,.pdf,.rar,.zip">
            </div>
            <div class="form-group my-1">
                <label>
                    Công văn đồng ý gia hạn (doc,docx,pdf,rar,zip)
                </label>
                <input class="form-control" type="file" name="renewal_approval_letter"
                    accept=".doc,.docx,.pdf,.rar,.zip">
            </div>
            <div class="form-group my-1">
                <label>
                    Phụ lục gia hạn (doc,docx,pdf,rar,zip)
                </label>
                <input class="form-control" type="file" name="renewal_appendix" accept=".doc,.docx,.pdf,.rar,.zip">
            </div>
            <div class="form-group my-1">
                <label>
                    Hồ sơ khác (doc,docx,pdf,rar,zip)
                </label>
                <input class="form-control" type="file" name="other_documents" accept=".doc,.docx,.pdf,.rar,.zip">
            </div>
            <div class="form-group my-1">
                <label>
                    Giá trị điều chỉnh(vnđ)
                </label>
                <input class="form-control" type="text" name="adjusted_value" id="appendix-adjusted-value">
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

    <x-modal id="contract-finance-modal" title="Tài chính" size="md" nested="true">
        <x-slot:body>
            <input name="contract_id" hidden>
            <div class="form-group my-1">
                <label>
                    Đơn vị
                </label>
                <select name="contract_unit_id" required>
                    <x-select-options :items="$contractUnits"></x-select-options>
                </select>
            </div>
            <div class="form-group my-1">
                <label>
                    Vai trò
                </label>
                <select name="role" required>
                    <x-select-options :items="$financeRoles" keyField="original" valueFields="converted"></x-select-options>
                </select>
            </div>
            <div class="form-group my-1">
                <label>
                    Giá trị thực hiện(vnđ)
                </label>
                <input class="form-control" type="text" name="realized_value" id="finance-realized-value" required>
            </div>
            <div class="form-group my-1">
                <label>
                    Giá trị nghiệm thu(vnđ)
                </label>
                <input class="form-control" type="text" name="acceptance_value" id="finance-acceptance-value"
                    required>
            </div>
            <div class="form-group my-1">
                <label>
                    Mức thuế(%)
                </label>
                <input class="form-control" type="text" name="vat_rate" id="finance-vat-rate" required>
            </div>
            <div class="form-group my-1">
                <label>
                    VAT(vnđ)
                </label>
                <input class="form-control bg-light" type="text" name="vat_amount" id="finance-vat-amount" readonly>
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

        const listAppendixUrl = @json(route('api.contract.appendix.list'));
        const storeAppendixUrl = @json(route('api.contract.appendix.store'));
        const updateAppendixUrl = @json(route('api.contract.appendix.update'));
        const deleteAppendixUrl = @json(route('contract.appendix.delete'));

        const listFinanceUrl = @json(route('api.contract.finance.list'));
        const storeFinanceUrl = @json(route('api.contract.finance.store'));
        const updateFinanceUrl = @json(route('api.contract.finance.update'));
        const deleteFinanceUrl = @json(route('contract.finance.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/contract/list.js"></script>
    <script src="assets/js/format/span-formatter.js"></script>
    <script src="assets/js/contract/vat-calculator.js"></script>
    <script src="assets/js/contract/detail-modal-content/script.js"></script>
    <script src="assets/js/contract/detail-modal-content/general-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/documents-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/bills-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/appendixes-info.js"></script>
    <script src="assets/js/contract/detail-modal-content/finances-info.js"></script>
@endsection
