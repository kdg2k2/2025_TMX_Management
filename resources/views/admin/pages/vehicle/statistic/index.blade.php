@extends('admin.layout.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.css">
    <link rel="stylesheet" href="assets/css/apex-chart/styles.css">
@endsection
@section('content')
    <x-breadcrumb :items="[['label' => 'Trang chủ', 'url' => route('dashboard')], ['label' => 'Thống kê phương tiện', 'url' => null]]">
    </x-breadcrumb>

    {{-- PHẦN 1: THỐNG KÊ HIỆN TẠI (không filter) --}}

    {{-- Cards trạng thái phương tiện hiện tại --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Trạng thái phương tiện hiện tại</div>
        </div>
        <div class="card-body">
            <div id="stats-container-current" class="row"></div>
        </div>
    </div>

    {{-- Cards cảnh báo --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Cảnh báo hết hạn (30 ngày tới)</div>
        </div>
        <div class="card-body">
            <div id="stats-container-warnings" class="row"></div>
        </div>
    </div>

    {{-- Biểu đồ trạng thái hiện tại --}}
    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Tỷ lệ trạng thái phương tiện</div>
                </div>
                <div class="card-body">
                    <div id="chart-status-pie"></div>
                </div>
            </div>
        </div>
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
    </div>

    {{-- PHẦN 2: BỘ LỌC THỐNG KÊ THEO THỜI GIAN --}}

    {{-- Filter --}}
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
                    <button id="btn-filter" class="btn btn-primary d-block w-100">
                        <i class="ti ti-filter me-1"></i> Lọc dữ liệu
                    </button>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button id="btn-reset" class="btn btn-secondary d-block w-100">
                        <i class="ti ti-refresh me-1"></i> Đặt lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẦN 3: THỐNG KÊ THEO THỜI GIAN (có filter) --}}

    {{-- Cards thống kê hoạt động --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Thống kê hoạt động</div>
        </div>
        <div class="card-body">
            <div id="stats-container-activity" class="row"></div>
        </div>
    </div>

    {{-- Biểu đồ theo thời gian - Hàng 1 --}}
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

    {{-- Biểu đồ theo thời gian - Hàng 2 --}}
    <div class="row mb-3 chart-card-row chart-row">
        <div class="col-md-12">
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

    {{-- Bảng chi tiết phương tiện trả về không ready --}}
    <div class="card custom-card mb-3">
        <div class="card-header">
            <div class="card-title">Chi tiết phương tiện trả về chưa rửa/lỗi/hỏng</div>
        </div>
        <div class="card-body">
            <div id="table-returned-not-ready"></div>
        </div>
    </div>

    {{-- Bảng cảnh báo hết hạn --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Xe sắp hết hạn đăng kiểm</div>
                </div>
                <div class="card-body">
                    <div id="table-inspection-expiry"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">Xe sắp hết hạn bảo hiểm TNDS</div>
                </div>
                <div class="card-body">
                    <div id="table-insurance-expiry"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js?v={{ time() }}"></script>
    <script src="assets/js/apex-chart-js/chart-base.js?v={{ time() }}"></script>
    <script>
        const apiVehicleStatisticData = @json(route('api.vehicle.statistic.data'));
    </script>
    <script src="assets/js/vehicle/statistic/script.js?v={{ time() }}"></script>
@endsection
