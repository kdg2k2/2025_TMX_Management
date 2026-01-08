@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Văn bản đến', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('official-document.incoming.create')" />
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
        const listUrl = @json(route('api.official-document.incoming.list'));
        const editUrl = @json(route('official-document.incoming.edit'));
        const deleteUrl = @json(route('official-document.incoming.delete'));
        const assignUrl = @json(route('official-document.incoming.assign'));
        const completeUrl = @json(route('official-document.incoming.complete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/incoming/list.js?v={{ time() }}"></script>
@endsection
