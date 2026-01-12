const statsContainerCurrent = document.getElementById(
    "stats-container-current"
);
const statsContainerActivity = document.getElementById(
    "stats-container-activity"
);

// Load data với filter
const loadData = async () => {
    const year =
        document.getElementById("filter-year")?.value ||
        new Date().getFullYear();
    const month = document.getElementById("filter-month")?.value || "";

    const params = { year };
    if (month) params.month = month;

    const res = await http.get(apiDeviceStatisticData, params);

    if (res?.data) {
        if (res.data.counter_current_status)
            renderStatsCards(
                res.data.counter_current_status,
                statsContainerCurrent,
                "col-sm-6 col-lg-4 col-xl-3"
            );

        if (res.data.counter_activity)
            renderStatsCards(res.data.counter_activity, statsContainerActivity);

        if (res.data.charts) renderCharts(res.data.charts);

        if (res.data.loan_returned_not_normal_detail)
            renderReturnedNotNormalTable(
                res.data.loan_returned_not_normal_detail
            );
    }
};

// Render all charts
const renderCharts = (charts) => {
    // 1. Biểu đồ tròn - Trạng thái thiết bị (KHÔNG filter)
    if (charts.status_pie) {
        createPieChart(
            "chart-status-pie",
            charts.status_pie.labels,
            charts.status_pie.series,
            null,
            "",
            "100%",
            "100%",
            "thiết bị"
        );
    }

    // 2. Biểu đồ cột chồng - Trạng thái theo loại (KHÔNG filter)
    if (charts.status_by_type) {
        createStackedBarChart(
            "chart-status-by-type",
            charts.status_by_type.categories,
            charts.status_by_type.series,
            "",
            "100%",
            "100%",
            "thiết bị"
        );
    }

    // 3. Biểu đồ cột - Lượt mượn theo tháng (CÓ filter)
    if (charts.loan_by_month) {
        createBarChart(
            "chart-loan-by-month",
            charts.loan_by_month.categories,
            charts.loan_by_month.series[0].data,
            ["#0d6efd"],
            charts.loan_by_month.series[0].name,
            "",
            "Số lượt",
            null,
            "100%",
            "100%",
            "lượt",
            false
        );
    }

    // 4. Biểu đồ kết hợp - Chi phí và số lượt sửa chữa (CÓ filter)
    if (charts.fix_cost_by_month) {
        createMixedChart(
            "chart-fix-cost-by-month",
            charts.fix_cost_by_month.categories,
            charts.fix_cost_by_month.series[0].data,
            charts.fix_cost_by_month.series[1].data,
            charts.fix_cost_by_month.series[0].name,
            charts.fix_cost_by_month.series[1].name,
            "",
            "Số lượt sửa",
            "Chi phí (VNĐ)",
            "100%",
            "100%",
            "lượt",
            "vnđ"
        );
    }
};

// Render bảng thiết bị trả về không normal
const renderReturnedNotNormalTable = (data) => {
    const container = document.getElementById("table-returned-not-normal");

    if (!data || data.length === 0) {
        container.innerHTML =
            '<p class="text-muted text-center">Không có dữ liệu</p>';
        return;
    }

    let html = `
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>Thiết bị</th>
                    <th>Loại thiết bị</th>
                    <th>Người mượn</th>
                    <th>Ngày mượn</th>
                    <th>Ngày trả</th>
                    <th class="text-center">Trạng thái khi trả</th>
                </tr>
            </thead>
            <tbody>
    `;

    const statusConfig = {
        broken: { color: "danger", label: "Hỏng", icon: "ti ti-x" },
        faulty: { color: "warning", label: "Lỗi", icon: "ti ti-alert-circle" },
        lost: { color: "dark", label: "Thất lạc", icon: "ti ti-help" },
    };

    data.forEach((item, index) => {
        const status = statusConfig[item.device_status_return] || {
            color: "secondary",
            label: item.device_status_return,
            icon: "ti ti-circle",
        };

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>
                    <div class="fw-semibold">${item.device?.name || "N/A"}</div>
                    <small class="text-muted">${item.device?.code || ""}</small>
                </td>
                <td>${item.device?.device_type?.name || "N/A"}</td>
                <td>${item.created_by?.name || "N/A"}</td>
                <td>${formatDateTime(item.borrowed_date) || "N/A"}</td>
                <td>${formatDateTime(item.returned_at) || "N/A"}</td>
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
    `;

    container.innerHTML = html;
    initDataTable($(container.querySelector("table")));
};

// Reset filter
const resetFilter = () => {
    document.getElementById("filter-year").value = new Date().getFullYear();
    document.getElementById("filter-month").value = "";
    loadData();
};

// Event listeners
document.addEventListener("DOMContentLoaded", async () => {
    await loadData();

    // Filter button
    document
        .getElementById("btn-filter")
        ?.addEventListener("click", async () => {
            await loadData();
        });

    // Reset button
    document.getElementById("btn-reset")?.addEventListener("click", () => {
        resetFilter();
        refreshSumoSelect();
    });
});
