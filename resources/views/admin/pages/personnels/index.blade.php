@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Nhân sự', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('personnels.create')" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <x-nav-tab style="pills" :tabs="[
                [
                    'title' => 'Thông Tin Chung',
                    'icon' => 'ti ti-info-circle',
                    'content' => view('admin.pages.personnels.table-info')->render(),
                ],
                [
                    'class' => 'border-0',
                    'title' => 'Tổng hợp',
                    'icon' => 'ti ti-files',
                    'content' => view('admin.pages.personnels.synthetic-excel')->render(),
                ],
            ]" />
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.personnels.list'));
        const synctheticExcelUrl = @json(route('api.personnels.syncthetic-excel'));
        const editUrl = @json(route('personnels.edit'));
        const deleteUrl = @json(route('personnels.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/personnels/list.js"></script>
    <script src="assets/js/personnels/synthetic-excel.js"></script>
@endsection
