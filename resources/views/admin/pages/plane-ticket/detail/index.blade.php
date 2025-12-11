@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Vé máy bay', 'url' => null],
        ['label' => 'Chi tiết', 'url' => null],
    ]">
        <x-button variant="primary" size="sm" icon="ti ti-list" tooltip="Danh sách" :href="route('plane-ticket.index')" />
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
        var customDataTableFilterParams = {
            plane_ticket_id: new URLSearchParams(window.location.search).get('id')
        };
        const listUrl = @json(route('api.plane-ticket.detail.list'));
        const editUrl = @json(route('plane-ticket.detail.edit'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/plane-ticket/detail/list.js?v={{ time() }}"></script>
@endsection
