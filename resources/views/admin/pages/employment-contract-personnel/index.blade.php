@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Nhân sự', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('employment-contract-personnel.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <x-nav-tab style="pills" :tabs="[
                [
                    'title' => 'Thông Tin Chung',
                    'icon' => 'ti ti-info-circle',
                    'content' => view('admin.pages.employment-contract-personnel.table-info')->render(),
                ],
                [
                    'class' => 'border-0',
                    'title' => 'Tổng hợp',
                    'icon' => 'ti ti-chart-bar',
                    'content' => view('admin.pages.employment-contract-personnel.synthetic-excel')->render(),
                ],
            ]" />
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.employment-contract-personnel.list'));
        const synctheticExcelUrl = @json(route('api.employment-contract-personnel.syncthetic-excel'));
        const editUrl = @json(route('employment-contract-personnel.edit'));
        const deleteUrl = @json(route('employment-contract-personnel.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/employment-contract-personnel/list.js"></script>
    <script src="assets/js/employment-contract-personnel/synthetic-excel.js"></script>
@endsection
