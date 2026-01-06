const statsContainerCurrent = document.getElementById("stats-container-current");
const statsContainerActivity = document.getElementById("stats-container-activity");
const statsContainerWarnings = document.getElementById("stats-container-warnings");

// Load data với filter
const loadData = async () => {
    const year = document.getElementById("filter-year")?.value || new Date().getFullYear();
    const month = document.getElementById("filter-month")?.value || '';

    const params = { year };
    if (month) params.month = month;

    const res = await http.get(apiVehicleStatisticData, params);

    if (res?.data) {
        // Render 3 nhóm counter
        if (res.data.counter_current_status)
            renderStatsCards(res.data.counter_current_status, statsContainerCurrent, 'col-md-6 col-xl-4');

        if (res.data.counter_warnings)
            renderStatsCards(res.data.counter_warnings, statsContainerWarnings, 'col-md-6');

        if (res.data.counter_activity)
            renderStatsCards(res.data.counter_activity, statsContainerActivity, 'col-md-6 col-xl-4');

        if (res.data.charts)
            renderCharts(res.data.charts);

        if (res.data.loan_returned_not_ready_detail)
            renderReturnedNotReadyTable(res.data.loan_returned_not_ready_detail);

        if (res.data.expiry_warnings)
            renderExpiryTables(res.data.expiry_warnings);
    }
};

// Hàm render cards - dùng chung
const renderStatsCards = (counter, container, classSize = 'col-md-6 col-xl-3') => {
    container.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = classSize + " mb-3";

        colDiv.innerHTML = `
            <div class="card custom-card dashboard-main-card overflow-hidden ${element?.color}">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-${element?.color} rounded">
                                <i class="${element?.icon} fs-2"></i>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <span class="d-block text-muted mb-1">${element?.converted}</span>
                            <h3 class="fw-semibold mb-0">
                                ${fmNumber(element?.value)}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.appendChild(colDiv);
    });
};

// Render all charts
const renderCharts = (charts) => {
    // 1. Biểu đồ tròn - Trạng thái phương tiện (KHÔNG filter)
    if (charts.status_pie) {
        createPieChart(
            'chart-status-pie',
            charts.status_pie.labels,
            charts.status_pie.series,
            null,
            '',
            '100%',
            '100%',
            'phương tiện'
        );
    }

    // 2. Biểu đồ ngang - Top xe (KHÔNG filter - theo filter time)
    if (charts.top_vehicles) {
        createBarChart(
            'chart-top-vehicles',
            charts.top_vehicles.categories,
            charts.top_vehicles.series[0].data,
            null,
            charts.top_vehicles.series[0].name,
            '',
            null,
            null,
            '100%',
            '100%',
            'lượt',
            true // horizontal
        );
    }

    // 3. Biểu đồ cột - Lượt mượn theo tháng (CÓ filter)
    if (charts.loan_by_month) {
        createBarChart(
            'chart-loan-by-month',
            charts.loan_by_month.categories,
            charts.loan_by_month.series[0].data,
            ['#0d6efd'],
            charts.loan_by_month.series[0].name,
            '',
            'Số lượt',
            null,
            '100%',
            '100%',
            'lượt',
            false
        );
    }

    // 4. Biểu đồ kết hợp - Chi phí xăng & bảo dưỡng (CÓ filter)
    if (charts.cost_by_month) {
        createMixedChart(
            'chart-cost-by-month',
            charts.cost_by_month.categories,
            charts.cost_by_month.series[0].data,
            charts.cost_by_month.series[1].data,
            charts.cost_by_month.series[0].name,
            charts.cost_by_month.series[1].name,
            '',
            'Chi phí xăng (VNĐ)',
            'Chi phí bảo dưỡng (VNĐ)',
            '100%',
            '100%',
            '₫',
            '₫'
        );
    }

    // 5. Biểu đồ đường - Tổng km theo tháng (CÓ filter)
    if (charts.km_by_month) {
        createLineChart(
            'chart-km-by-month',
            charts.km_by_month.categories,
            charts.km_by_month.series[0].data,
            '#28a745',
            null,
            'Tổng km',
            ''
        );
    }
};

// Render bảng phương tiện trả về không ready
const renderReturnedNotReadyTable = (data) => {
    const container = document.getElementById("table-returned-not-ready");

    if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Không có dữ liệu</p>';
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">STT</th>
                        <th>Phương tiện</th>
                        <th>Người mượn</th>
                        <th>Ngày mượn</th>
                        <th>Ngày trả</th>
                        <th class="text-center">Trạng thái khi trả</th>
                    </tr>
                </thead>
                <tbody>
    `;

    const statusConfig = {
        'unwashed': { color: 'purple', label: 'Chưa rửa', icon: 'ti ti-car-crash' },
        'broken': { color: 'danger', label: 'Hỏng', icon: 'ti ti-x' },
        'faulty': { color: 'warning', label: 'Lỗi', icon: 'ti ti-alert-circle' },
        'lost': { color: 'dark', label: 'Thất lạc', icon: 'ti ti-help' }
    };

    data.forEach((item, index) => {
        const status = statusConfig[item.vehicle_status_return] || {
            color: 'secondary',
            label: item.vehicle_status_return,
            icon: 'ti ti-circle'
        };

        const vehicleName = [item.vehicle?.brand, item.vehicle?.license_plate]
            .filter(Boolean)
            .join(' - ') || 'N/A';

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${vehicleName}</td>
                <td>${item.created_by?.name || 'N/A'}</td>
                <td>${formatDateTime(item.vehicle_pickup_time) || 'N/A'}</td>
                <td>${formatDateTime(item.returned_at) || 'N/A'}</td>
                <td class="text-center">
                    <span class="badge bg-${status.color}">
                        <i class="${status.icon} me-1"></i>
                        ${status.label}
                    </span>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
};

// Render bảng cảnh báo hết hạn
const renderExpiryTables = (warnings) => {
    // Bảng đăng kiểm
    renderExpiryTable(
        'table-inspection-expiry',
        warnings.inspection,
        'inspection_expired_at',
        'Hạn đăng kiểm'
    );

    // Bảng bảo hiểm
    renderExpiryTable(
        'table-insurance-expiry',
        warnings.liability_insurance,
        'liability_insurance_expired_at',
        'Hạn bảo hiểm TNDS'
    );
};

const renderExpiryTable = (containerId, data, dateField, label) => {
    const container = document.getElementById(containerId);

    if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Không có xe sắp hết hạn</p>';
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">STT</th>
                        <th>Phương tiện</th>
                        <th>${label}</th>
                        <th class="text-center">Còn lại</th>
                    </tr>
                </thead>
                <tbody>
    `;

    data.forEach((item, index) => {
        const vehicleName = [item.brand, item.license_plate]
            .filter(Boolean)
            .join(' - ') || 'N/A';

        const expiredDate = item[dateField];
        const daysLeft = expiredDate ? Math.ceil((new Date(expiredDate) - new Date()) / (1000 * 60 * 60 * 24)) : 0;
        const badgeColor = daysLeft <= 7 ? 'danger' : (daysLeft <= 15 ? 'warning' : 'info');

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${vehicleName}</td>
                <td>${formatDateTime(expiredDate) || 'N/A'}</td>
                <td class="text-center">
                    <span class="badge bg-${badgeColor}">
                        ${daysLeft} ngày
                    </span>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
};

// Reset filter
const resetFilter = () => {
    document.getElementById("filter-year").value = new Date().getFullYear();
    document.getElementById("filter-month").value = '';
    loadData();
};

// Event listeners
document.addEventListener("DOMContentLoaded", async () => {
    await loadData();

    // Filter button
    document.getElementById("btn-filter")?.addEventListener("click", async () => {
        await loadData();
    });

    // Reset button
    document.getElementById("btn-reset")?.addEventListener("click", () => {
        resetFilter();
    });
});
