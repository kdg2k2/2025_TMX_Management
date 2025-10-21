@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Xây dựng gói thầu', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('bidding.create')" />
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
        const listUrl = @json(route('api.bidding.list'));
        const editUrl = @json(route('bidding.edit'));
        const deleteUrl = @json(route('bidding.delete'));
        const showUrl = @json(route('bidding.show'));
    </script>
    <script src="assets/js/bidding/list.js"></script>
@endsection
