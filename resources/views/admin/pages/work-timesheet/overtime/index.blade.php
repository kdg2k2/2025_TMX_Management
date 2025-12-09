@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Nộp bảng chấm công', 'url' => null],
        ['label' => auth()->user()->department->name . ' - ' . $currentMonth . '/' . $currentYear, 'url' => null],
    ]">
        <x-button variant="success" size="sm" icon="ti ti-download" tooltip="Export" class="me-1"
            onclick="downloadTemplate()" />
        <x-button variant="warning" size="sm" icon="ti ti-upload" tooltip="Upload" class="me-1"
            onclick="openModalUpload()" />
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <table class="display w-100" id="datatable"></table>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-upload" title="Tải lên bảng chấm công tháng: {{ $currentMonth }}/{{ $currentYear }}" size="md"
        method="post" nested="true" :action="route('api.work-timesheet.overtime.upload')">
        <x-slot:body>
            <input class="form-control" type="hidden" name="month" value="{{ $currentMonth }}" required>
            <input class="form-control" type="hidden" name="year" value="{{ $currentYear }}" required>
            <div class="my-1">
                <label>
                    File bảng chấm công (.xlsx)
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
        const $month = @json($currentMonth);
        const $year = @json($currentYear);
        const listUrl = @json(route('api.work-timesheet.overtime.detail.list'));
        const apiWorkTimesheetOvertimeTemplate = @json(route('api.work-timesheet.overtime.template'));
    </script>
    <script src="assets/js/http-request/base-list.js?v={{ time() }}"></script>
    <script src="assets/js/work-timesheet/overtime/script.js?v={{ time() }}"></script>
@endsection
