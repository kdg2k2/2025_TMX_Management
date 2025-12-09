@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Sở hữu phần mềm', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('software_ownerships.create')" />
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
        const listUrl = @json(route('api.software_ownerships.list'));
        const editUrl = @json(route('software_ownerships.edit'));
        const deleteUrl = @json(route('software_ownerships.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/software_ownerships/list.js?v={{ time() }}"></script>
@endsection
