@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Loại tài liệu hồ sơ chuyên môn', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('professional-record.type.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.professional-record.type.list'));
        const editUrl = @json(route('professional-record.type.edit'));
        const deleteUrl = @json(route('professional-record.type.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/professional-record/type/list.js?v={{ time() }}"></script>
@endsection
