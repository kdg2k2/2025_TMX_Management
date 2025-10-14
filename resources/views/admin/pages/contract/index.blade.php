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
            <div class="form-group my-1">
                <label>
                    Loại file
                </label>
                <select name="type_id" id="contract-file-type" required>
                    <x-select-options :items="$fileTypes" recordAttribute="data-record"></x-select-options>
                </select>
            </div>
            <div class="form-group my-1">
                <label id="contract-file-label">
                    Chọn file
                </label>
                <input class="form-control" type="file" name="path" id="contract-file-input" required>
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
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/contract/list.js"></script>
    <script src="assets/js/contract/detail-modal-content/script.js"></script>
    <script src="assets/js/contract/detail-modal-content/general-info.js"></script>
    <script src="assets/js/http-request/base-store-and-update.js"></script>
    <script src="assets/js/contract/detail-modal-content/documents-info.js"></script>
@endsection
