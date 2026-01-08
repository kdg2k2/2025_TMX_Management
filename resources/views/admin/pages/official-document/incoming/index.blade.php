@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Văn bản đến', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('official-document.incoming.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        @include('admin/pages/official-document/incoming/base-inputs')
        <div class="col-lg-2 col-md-6">
            <div class="form-group">
                <label>
                    Trạng thái nhiệm vụ
                </label>
                <select name="status" id="status" class="form-control">
                    <x-select-options :items="$status" keyField="original" valueFields="converted" />
                </select>
            </div>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="assign-modal" title="Giao nhiệm vụ" size="md" method="POST" nested="true">
        <x-slot:body>
            <div class="my-1">
                <label>
                    Người thực hiện nhiệm vụ
                </label>
                <select name="task_assignee_id" class="form-control" required>
                    <x-select-options :items="$users" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    Thành viên hỗ trợ
                </label>
                <select name="users[]" class="form-control" multiple>
                    <x-select-options :items="$users" :emptyOption="false" />
                </select>
            </div>
            <div class="my-1">
                <label>
                    Thời hạn hoàn thành
                </label>
                <input type="date" class="form-control" name="task_completion_deadline" required>
            </div>
            <div class="my-1">
                <label>
                    Ghi chú
                </label>
                <input type="text" class="form-control" name="task_notes">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>

    <x-modal id="complete-modal" title="Hoàn thành nhiệm vụ" size="sm" method="POST" nested="true">
        <x-slot:body>
            Chắc chắn đã hoàn thành nhiệm vụ?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.official-document.incoming.list'));
        const editUrl = @json(route('official-document.incoming.edit'));
        const deleteUrl = @json(route('official-document.incoming.delete'));
        const assignUrl = @json(route('official-document.incoming.assign'));
        const completeUrl = @json(route('official-document.incoming.complete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/incoming/modals.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/incoming/list.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/incoming/filter.js?v={{ time() }}"></script>
@endsection
