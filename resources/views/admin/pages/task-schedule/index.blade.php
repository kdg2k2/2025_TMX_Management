@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Tiến trình gửi mail tự động', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-run" title="Xác nhận" size="sm" method="post" nested="true">
        <x-slot:body>
            Chắc chắn chạy tác vụ này?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit variant="primary" />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.task-schedule.list'));
        const editUrl = @json(route('task-schedule.edit'));
        const runUrl = @json(route('api.task-schedule.run'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/task-schedule/list.js?v={{ time() }}"></script>
@endsection
