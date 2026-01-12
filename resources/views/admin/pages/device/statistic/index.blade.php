@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.css">
    <link rel="stylesheet" href="assets/css/apex-chart/styles.css">
@endsection
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Thống kê thiết bị', 'url' => null]]">
    </x-breadcrumb>

    {{-- Cards trạng thái thiết bị hiện tại --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            Trạng thái thiết bị hiện tại
        </div>
        <div class="card-body">
            <div id="stats-container-current" class="row"></div>
        </div>
    </div>

    {{-- Biểu đồ trạng thái hiện tại --}}
    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-lg-6 col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    Tỷ lệ trạng thái thiết bị
                </div>
                <div class="card-body">
                    <div id="chart-status-pie"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    Trạng thái theo loại thiết bị
                </div>
                <div class="card-body">
                    <div id="chart-status-by-type"></div>
                </div>
            </div>
        </div>
    </div>

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
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <x-button id="btn-filter" variant="primary" class="w-100" size="md">
                        Lọc dữ liệu
                    </x-button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <x-button id="btn-reset" variant="secondary" class="w-100" size="md">
                        Đặt lại
                    </x-button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards thống kê mượn/sửa chữa --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            Thống kê hoạt động
        </div>
        <div class="card-body">
            <div id="stats-container-activity" class="row"></div>
        </div>
    </div>

    {{-- Biểu đồ theo thời gian --}}
    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-lg-6 col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    Số lượt mượn theo tháng
                </div>
                <div class="card-body">
                    <div id="chart-loan-by-month"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="card custom-card">
                <div class="card-header">
                    Chi phí & số lượt sửa chữa
                </div>
                <div class="card-body">
                    <div id="chart-fix-cost-by-month"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng chi tiết thiết bị trả về bị lỗi --}}
    <div class="card custom-card">
        <div class="card-header">
            Chi tiết thiết bị trả về bị lỗi/hỏng
        </div>
        <div class="card-body">
            <div id="table-returned-not-normal"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const apiDeviceStatisticData = @json(route('api.device.statistic.data'));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js?v={{ time() }}"></script>
    <script src="assets/js/apex-chart-js/chart-base.js?v={{ time() }}"></script>
    <script src="assets/js/device/statistic/script.js?v={{ time() }}"></script>
@endsection
