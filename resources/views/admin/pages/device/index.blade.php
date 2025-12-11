@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="assets/css/bootstrap-carousel/style.css">
@endsection
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Thiết bị', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('device.create')" />
    </x-breadcrumb>

    <div class="mb-2 row">
        @include('admin.pages.device.filter-content')
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
        const listUrl = @json(route('api.device.list'));
        const editUrl = @json(route('device.edit'));
        const deleteUrl = @json(route('device.delete'));
        const deviceImageIndex = @json(route('device.image.index'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/device/list.js?v={{ time() }}"></script>
    <script src="assets/js/bootstrap-carousel/script.js?v={{ time() }}"></script>
    <script src="assets/js/gallery/script.js?v={{ time() }}"></script>
    <script src="assets/js/device/filter.js?v={{ time() }}"></script>
@endsection
