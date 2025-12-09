@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Tài khoản', 'url' => route('user.index')],
        ['label' => 'Email phụ', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('user.sub-email.create', [
            'user_id' => $userId,
        ])" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const $userId = @json($userId);
        const table = $('#datatable');
        const listUrl = @json(route('api.user.sub-email.list'));
        const editUrl = @json(route('user.sub-email.edit'));
        const deleteUrl = @json(route('api.user.sub-email.delete'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/user/sub-email/list.js?v={{ time() }}"></script>
@endsection
