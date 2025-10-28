@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Tài khoản', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('user.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4">
            <select id="department_id">
                <x-select-options :items="$departments" emptyText="Phòng ban"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="position_id">
                <x-select-options :items="$positions" emptyText="Chức vụ"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="job_title_id">
                <x-select-options :items="$jobTitles" emptyText="Chức danh"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="role_id">
                <x-select-options :items="$roles" emptyText="Quyền truy cập"></x-select-options>
            </select>
        </div>
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
        const listSubEmailUrl = @json(route('user.sub-email.index'));
        const listUrl = @json(route('api.user.list'));
        const editUrl = @json(route('user.edit'));
        const deleteUrl = @json(route('user.delete'));
    </script>
    <script src="assets/js/user/list.js"></script>
@endsection
