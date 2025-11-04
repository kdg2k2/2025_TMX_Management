@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Nhân sự', 'url' => route('employment-contract-personnel.index')],
        ['label' => 'Cột thông tin bổ sung', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('employment-contract-personnel.custom-field.create')" />
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
        const listUrl = @json(route('api.employment-contract-personnel.custom-field.list'));
        const editUrl = @json(route('employment-contract-personnel.custom-field.edit'));
        const deleteUrl = @json(route('employment-contract-personnel.custom-field.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/employment-contract-personnel/custom-field/list.js"></script>
@endsection
