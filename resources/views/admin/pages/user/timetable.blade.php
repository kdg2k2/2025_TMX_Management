@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Tài khoản', 'url' => route('user.index')],
        ['label' => 'Thời gian biểu', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="row mb-2">
        <div class="col-lg-2 col-md-4">
            <select id="year">
                <x-select-options :items="$years" :emptyOption="false" :selected="(int) date('Y')" />
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="week">
                <x-select-options :items="$weeks" :emptyOption="false" :selected="$currentWeekNumber" keyField="week_number"
                    valueFields="label" />
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select id="department">
                <x-select-options :items="$departments" emptyText="Phòng ban" />
            </select>
        </div>
    </div>
    <div class="card custom-card">
        <div class="card-body">
            <div id="timetable-container"></div>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-warning" title="Xác nhận" method="POST" size="md">
        <x-slot:body>
            Có chắc chắn thực hiện cảnh báo?
        </x-slot:body>
        <x-slot:footer>
            <x-button variant="light" outline="true" size="sm" icon="ti ti-x" text="Đóng" data-bs-dismiss="modal" />
            <x-button-submit />
        </x-slot:footer>
    </x-modal>
@endsection
@section('scripts')
    <script>
        const apiUserTimetableList = @json(route('api.user.timetable.list'));
        const apiUserTimetableGetWeeks = @json(route('api.user.timetable.get-weeks'));
        const apiUserWarningStore = @json(route('api.user.warning.store'));
    </script>
    <script src="assets/js/user/timetable.js"></script>
@endsection
