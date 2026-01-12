@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.css">
    <link rel="stylesheet" href="assets/css/apex-chart/styles.css">
@endsection
@section('content')
    <x-breadcrumb :items="[
        ['label' => 'Trang chủ', 'url' => route('dashboard')],
        ['label' => 'Thống kê phương tiện', 'url' => null],
    ]">
    </x-breadcrumb>

    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Trạng thái phương tiện hiện tại</div>
        </div>
        <div class="card-body">
            <div id="stats-container-current" class="row"></div>
        </div>
    </div>

    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Cảnh báo hết hạn (10 ngày tới)</div>
        </div>
        <div class="card-body">
            <div id="stats-container-warnings" class="row"></div>
        </div>
    </div>

    <div class="card custom-card mb-3">
        <div class="card-header bg-light">
            <div class="card-title">Lọc theo thời gian</div>
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


    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Thống kê hoạt động</div>
        </div>
        <div class="card-body">
            <div id="stats-container-activity" class="row"></div>
        </div>
    </div>

    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Số lượt mượn theo tháng</div>
                </div>
                <div class="card-body">
                    <div id="chart-loan-by-month"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Chi phí xăng & bảo dưỡng</div>
                </div>
                <div class="card-body">
                    <div id="chart-cost-by-month"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Top 5 xe được mượn nhiều nhất</div>
                </div>
                <div class="card-body">
                    <div id="chart-top-vehicles"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Tổng số km đã chạy theo tháng</div>
                </div>
                <div class="card-body">
                    <div id="chart-km-by-month"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('modals')
    <x-modal id="modal-stat-detail" title="Chi tiết" size="xl">
        <x-slot:body>
            <div id="modal-stat-detail-content">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </x-slot:body>
    </x-modal>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js?v={{ time() }}"></script>
    <script src="assets/js/apex-chart-js/chart-base.js?v={{ time() }}"></script>
    <script>
        const apiVehicleStatisticData = @json(route('api.vehicle.statistic.data'));
        const apiVehicleStatisticDetail = @json(route('api.vehicle.statistic.detail'));
    </script>
    <script src="assets/js/vehicle/statistic/script.js?v={{ time() }}"></script>
@endsection
