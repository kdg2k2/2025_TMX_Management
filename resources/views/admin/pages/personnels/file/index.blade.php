@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Nhân sự', 'url' => route('personnels.index')],
        ['label' => 'Bằng cấp trình độ', 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-plus" tooltip="Thêm mới" :href="route('personnels.file.create')" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4">
            <select id="type_id">
                <x-select-options :items="$personnelFileTypes" emptyText="Loại file"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="personnel_id">
                <x-select-options :items="$personnels" emptyText="Nhân sự"></x-select-options>
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
        const listUrl = @json(route('api.personnels.file.list'));
        const editUrl = @json(route('personnels.file.edit'));
        const deleteUrl = @json(route('personnels.file.delete'));
    </script>
    <script src="assets/js/personnels/file/list.js"></script>
@endsection
