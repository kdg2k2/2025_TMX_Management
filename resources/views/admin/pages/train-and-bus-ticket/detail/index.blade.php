@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Vé tàu xe', 'url' => null],
        ['label' => 'Chi tiết', 'url' => null],
    ]">
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
            train_and_bus_ticket_id: new URLSearchParams(window.location.search).get('id')
        };
        const listUrl = @json(route('api.train-and-bus-ticket.detail.list'));
        const editUrl = @json(route('train-and-bus-ticket.detail.edit'));
    </script>
    <script src="assets/js/http-request/base-list.js"></script>
    <script src="assets/js/train-and-bus-ticket/detail/list.js"></script>
@endsection
