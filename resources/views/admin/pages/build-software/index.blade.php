@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'ĐXXD Phần mềm', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('build-software.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4">
            <select id="status">
                <x-select-options :items="$status" keyField="original" valueFields="converted"
                    emptyText="Trạng thái duyệt"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="state">
                <x-select-options :items="$state" keyField="original" valueFields="converted"
                    emptyText="Tình trạng"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="development-case">
                <x-select-options :items="$developmentCases" keyField="original" valueFields="converted"
                    emptyText="Trường hợp xây dựng"></x-select-options>
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
    <x-modal id="accept-modal" title="Phê duyệt" size="sm" nested="true" method="post">
        <x-slot:body>
            <p class="m-0">
                Chắc chắn phê duyệt?
            </p>
        </x-slot:body>
        <x-slot:footer>
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="reject-modal" title="Từ chối" size="sm" nested="true" method="post">
        <x-slot:body>
            <div class="form-group">
                <label>
                    Lý do từ chối
                </label>
                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="update-state-modal" title="Phê duyệt" size="sm" nested="true" method="post">
        <x-slot:body>
            <div class="form-group">
                <label>
                    Tình trạng
                </label>
                <select name="state" required>
                    <x-select-options :items="$state" keyField="original" valueFields="converted"></x-select-options>
                </select>
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
        const listUrl = @json(route('api.build-software.list'));
        const editUrl = @json(route('build-software.edit'));
        const deleteUrl = @json(route('build-software.delete'));
        const acceptUrl = @json(route('build-software.accept'));
        const rejectUrl = @json(route('build-software.reject'));
        const updateStateUrl = @json(route('build-software.update-state'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/build-software/list.js?v={{ time() }}"></script>
    <script src="assets/js/build-software/modals.js?v={{ time() }}"></script>
@endsection
