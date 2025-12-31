@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Phương tiện', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('vehicle.create')" />
    </x-breadcrumb>

    <div class="row">
        @include('admin.pages.vehicle.filter-content')
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const table = $('#datatable');
        const listUrl = @json(route('api.vehicle.list'));
        const editUrl = @json(route('vehicle.edit'));
        const deleteUrl = @json(route('vehicle.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/filter.js?v={{ time() }}"></script>
    <script src="assets/js/vehicle/list.js?v={{ time() }}"></script>
@endsection
