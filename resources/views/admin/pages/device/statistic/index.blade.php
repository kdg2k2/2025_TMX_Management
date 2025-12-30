@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.css">
@endsection
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Thống kê thiết bị', 'url' => null]]">
    </x-breadcrumb>

    <div class="card custom-card">
        <div class="card-body">
            <div id="stats-container" class="row"></div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js?v={{ time() }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js?v={{ time() }}"></script>
    <script src="assets/js/apex-chart-js/chart-base.js?v={{ time() }}"></script>
    <script>
        const apiDeviceStatisticData = @json(route('api.device.statistic.data'));
    </script>
    <script src="assets/js/device/statistic/script.js?v={{ time() }}"></script>
@endsection
