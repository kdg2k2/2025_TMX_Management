@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Bảng lương', 'url' => null]]">
        <x-button variant="success" size="sm" icon="ti ti-download" tooltip="Tải file đang hiển thị" class="me-1"
            onclick="downloadExcel()" />
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4 my-1">
            <select id="year">
                <x-select-options :items="$years" :emptyOption="false" :selected="$currentYear"></x-select-options>
            </select>
        </div>
        <div class="col-lg-2 col-md-4 my-1">
            <select id="month">
                <x-select-options :items="$months" :emptyOption="false" :selected="$currentMonth"></x-select-options>
            </select>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body">
            <div id="iframe-excel-container" class="d-none">
                <iframe id="iframe-excel" frameborder="0" style="height: 65vh; width: 100%;"></iframe>
            </div>
            <div id="none-data-container" class="d-flex justify-content-center">
                <h5 class="text-center mb-0">Chưa có dữ liệu</h5>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        const apiWorkTimesheetData = @json(route('api.work-timesheet.data'));
    </script>
    <script src="assets/js/work-timesheet/payroll/script.js"></script>
@endsection
