@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Đề nghị/Phát hành', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('official-document.create')" />
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
        const listUrl = @json(route('api.official-document.list'));
        const editUrl = @json(route('official-document.edit'));
        const deleteUrl = @json(route('official-document.delete'));
        const reviewApproveUrl = @json(route('official-document.review-approve'));
        const reviewRejectUrl = @json(route('official-document.review-reject'));
        const approveUrl = @json(route('official-document.approve'));
        const rejectUrl = @json(route('official-document.reject'));
        const releaseUrl = @json(route('official-document.release'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/official-document/list.js?v={{ time() }}"></script>
@endsection
