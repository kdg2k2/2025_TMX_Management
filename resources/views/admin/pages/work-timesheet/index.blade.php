@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Xuất lưới', 'url' => null]]">
        <x-button variant="warning" size="sm" icon="ti ti-upload" tooltip="Upload" class="me-1"
            onclick="openModalUpload()" />
        <x-button variant="success" size="sm" icon="ti ti-download" tooltip="Export" class="me-1"
            onclick="downloadExcel()" />
        <x-button variant="primary" size="sm" icon="ti ti-send" tooltip="Gửi mail xin ý kiến" onclick="" />
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
            <div id="iframe-excel-container" class="position-relative d-none">
                <x-button id="download-btn" class="position-absolute end-0" variant="success" :outline="true"
                    icon="ti ti-download" size="sm" tooltip="Tải"></x-button>
                <iframe id="iframe-excel" frameborder="0" style="height: 65vh; width: 100%;"></iframe>
            </div>
            <div id="none-data-container" class="d-flex justify-content-center">
                <h5 class="text-center mb-0">Chưa có dữ liệu</h5>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-upload" title="Tải lên xuất lưới tháng: {{ $currentMonth }}/{{ $currentYear }}" size="md" method="post"
        nested="true" :action="route('api.work-timesheet.import')">
        <x-slot:body>
            <input class="form-control" type="hidden" name="month" value="{{ $currentMonth }}" required>
            <input class="form-control" type="hidden" name="year" value="{{ $currentYear }}" required>
            <div class="my-1">
                <label>
                    Các ngày nghỉ lễ
                </label>
                <select name="holiday_days[]" multiple>
                    <x-select-options :items="$days" :emptyOption="false"></x-select-options>
                </select>
            </div>
            <div class="my-1">
                <label>
                    Các ngày mất điện
                </label>
                <select name="power_outage_days[]" multiple>
                    <x-select-options :items="$days" :emptyOption="false"></x-select-options>
                </select>
            </div>
            <div class="my-1">
                <label>
                    Các ngày làm bù
                </label>
                <select name="compensated_days[]" multiple>
                    <x-select-options :items="$days" :emptyOption="false"></x-select-options>
                </select>
            </div>
            <div class="my-1">
                <label>
                    File xuất lưới (.xlsx)
                </label>
                <input class="form-control" type="file" name="file" required accept=".xlsx">
            </div>
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const apiWorkTimesheetData = @json(route('api.work-timesheet.data'));
    </script>
    <script src="assets/js/work-timesheet/script.js"></script>
@endsection
