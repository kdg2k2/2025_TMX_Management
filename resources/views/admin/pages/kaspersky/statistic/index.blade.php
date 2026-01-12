@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.css">
    <link rel="stylesheet" href="assets/css/apex-chart/styles.css">
@endsection
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Thống kê mã kaspersky', 'url' => null],
    ]">
    </x-breadcrumb>

    {{-- Filter --}}
    <div class="card custom-card mb-3">
        <div class="card-header bg-light">
            Lọc theo thời gian
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Năm</label>
                    <select id="filter-year">
                        @for ($i = 2024; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                Năm {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tháng</label>
                    <select id="filter-month">
                        <option value="">Tất cả các tháng</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">Tháng {{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <x-button id="btn-filter" variant="primary" class="w-100" size="md">
                        Lọc dữ liệu
                    </x-button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <x-button id="btn-reset" variant="secondary" class="w-100" size="md">
                        Đặt lại
                    </x-button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <x-button id="btn-download" variant="success" class="w-100" icon="ti ti-download" size="md">
                        Tải biểu chi tiết
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards trạng thái thiết bị hiện tại --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            Số lượng mã
        </div>
        <div class="card-body">
            <div id="stats-container" class="row"></div>
        </div>
    </div>

    {{-- Bảng chi tiết --}}
    <div class="card custom-card">
        <div class="card-header">
            Chi tiết sử dụng mã
        </div>
        <div class="card-body">
            <iframe id="iframe-synthetic-excel" frameborder="0" style="height: 65vh; width: 100%;"></iframe>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const apiKasperskyStatisticData = @json(route('api.kaspersky.statistic.data'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js?v={{ time() }}"></script>
    <script src="assets/js/apex-chart-js/chart-base.js?v={{ time() }}"></script>
    <script src="assets/js/kaspersky/statistic/script.js?v={{ time() }}"></script>
@endsection
