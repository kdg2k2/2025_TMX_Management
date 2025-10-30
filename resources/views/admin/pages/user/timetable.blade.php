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
@section('scripts')
    <script>
        const apiUserTimetableList = @json(route('api.user.timetable.list'));
        const apiUserTimetableGetWeeks = @json(route('api.user.timetable.get-weeks'));
    </script>
    <script src="assets/js/user/timetable.js"></script>
@endsection
