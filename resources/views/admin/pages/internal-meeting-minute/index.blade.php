@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Biên bản họp giao ban', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('internal-meeting-minute.create')" />
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
        const listUrl = @json(route('api.internal-meeting-minute.list'));
        const editUrl = @json(route('internal-meeting-minute.edit'));
        const deleteUrl = @json(route('internal-meeting-minute.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/internal-meeting-minute/list.js?v={{ time() }}"></script>
@endsection
