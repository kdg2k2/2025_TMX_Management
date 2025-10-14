@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Hợp đồng', 'url' => null]]">
        <button class="btn btn-sm btn-success" onclick="window.location='{{ route('contract.create') }}'" type="button"
            data-bs-placement="top" data-bs-original-title="Thêm mới">
            <i class="ti ti-plus"></i>
        </button>
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
                        'content' => view('admin.contract.partials.general-info')->render(),
                    ],
                    [
                        'title' => 'Tài liệu',
                        'icon' => 'ti ti-files',
                        'badge' => ['text' => '0', 'color' => 'info'],
                        'content' => view('admin.contract.partials.documents-info')->render(),
                    ],
                ]" />
            </div>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Đóng</button>
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
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/contract/list.js"></script>
    <script src="assets/js/contract/detail-modal-content/script.js"></script>
    <script src="assets/js/contract/detail-modal-content/general-info.js"></script>
@endsection
