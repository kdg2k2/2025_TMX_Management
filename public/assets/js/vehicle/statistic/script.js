const statsContainerCurrent = document.getElementById(
    "stats-container-current"
);
const statsContainerActivity = document.getElementById(
    "stats-container-activity"
);
const statsContainerWarnings = document.getElementById(
    "stats-container-warnings"
);
let currentFilterParams = {};

// Load data với filter
const loadData = async () => {
    const year =
        document.getElementById("filter-year")?.value ||
        new Date().getFullYear();
    const month = document.getElementById("filter-month")?.value || "";

    const params = { year };
    if (month) params.month = month;

    currentFilterParams = params;

    const res = await http.get(apiVehicleStatisticData, params);

    if (res?.data) {
        // Render 3 nhóm counter
        if (res.data.counter_current_status)
            renderStatsCards(
                res.data.counter_current_status,
                statsContainerCurrent,
                "col-md-6 col-xl-4"
            );

        if (res.data.counter_warnings)
            renderStatsCards(
                res.data.counter_warnings,
                statsContainerWarnings,
                "col-md-4"
            ); // Đổi thành col-md-4 cho 3 cards

        if (res.data.counter_activity)
            renderStatsCards(
                res.data.counter_activity,
                statsContainerActivity,
                "col-md-6 col-xl-4"
            );

        if (res.data.charts) renderCharts(res.data.charts);
    }
};

// Hàm render cards - dùng chung
const renderStatsCards = (
    counter,
    container,
    classSize = "col-md-6 col-xl-3"
) => {
    container.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = classSize + " mb-3";

        // Thêm cursor pointer nếu có value > 0
        const cursorStyle = element?.value > 0 ? "cursor: pointer;" : "";
        const clickAttr =
            element?.value > 0
                ? `data-detail-key="${element.detail_key}" data-detail-filter="${element.detail_filter}"`
                : "";

        colDiv.innerHTML = `
            <div class="card custom-card dashboard-main-card overflow-hidden ${
                element?.color
            } stat-card"
                 style="${cursorStyle}"
                 ${clickAttr}>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-${
                                element?.color
                            } rounded">
                                <i class="${element?.icon} fs-2"></i>
                            </div>
                        </div>
                        <div class="flex-fill">
                            <span class="d-block text-muted mb-1">${
                                element?.converted
                            }</span>
                            <h3 class="fw-semibold mb-0">
                                ${fmNumber(element?.value)}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Thêm event listener cho click
        if (element?.value > 0) {
            const card = colDiv.querySelector(".stat-card");
            card.addEventListener("click", () => {
                openStatDetailModal(element);
            });
        }

        container.appendChild(colDiv);
    });
};

const openStatDetailModal = async (element) => {
    const modal = createModal(document.getElementById("modal-stat-detail"));
    const modalTitle = document.querySelector(
        "#modal-stat-detail .modal-title"
    );
    const modalContent = document.getElementById("modal-stat-detail-content");

    // Set title
    modalTitle.textContent = element.converted;

    // Show loading
    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Show modal
    modal.show();

    // Load data
    try {
        const params = {
            ...currentFilterParams,
            type: element.detail_key,
            filter: element.detail_filter,
        };

        const res = await http.get(apiVehicleStatisticDetail, params);

        if (res?.data) {
            renderDetailTable(modalContent, res.data, element.detail_key);
        }
    } catch (error) {
        modalContent.innerHTML =
            '<p class="text-danger text-center">Có lỗi xảy ra khi tải dữ liệu</p>';
    }
};

// Render detail table
const renderDetailTable = (container, data, type) => {
    if (!data || data.length === 0) {
        container.innerHTML =
            '<p class="text-muted text-center">Không có dữ liệu</p>';
        return;
    }

    let html =
        '<div class="table-responsive"><table class="table table-bordered table-hover" id="detail-table">';

    // Render header và body tùy theo type
    if (type === "vehicle_status") {
        html += renderVehicleTable(data);
    } else if (type === "vehicle_loan") {
        html += renderVehicleLoanTable(data);
    } else if (type === "warning") {
        html += renderWarningTable(data);
    }

    html += "</table></div>";
    container.innerHTML = html;

    // Initialize DataTable
    initDataTable($("#detail-table"));
};

// Render bảng xe
const renderVehicleTable = (data) => {
    let html = `
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 60px;">STT</th>
                <th>Hãng xe</th>
                <th>Biển số</th>
                <th>Số km</th>
                <th>Người sử dụng</th>
                <th>Điểm đến</th>
            </tr>
        </thead>
        <tbody>
    `;

    data.forEach((item, index) => {
        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${item.brand || "N/A"}</td>
                <td>${item.license_plate || "N/A"}</td>
                <td>${fmNumber(item.current_km)}</td>
                <td>${item.user?.name || "N/A"}</td>
                <td>${item.destination || "N/A"}</td>
            </tr>
        `;
    });

    html += "</tbody>";
    return html;
};

// Render bảng lượt mượn
const renderVehicleLoanTable = (data) => {
    let html = `
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 60px;">STT</th>
                <th>Phương tiện</th>
                <th>Người mượn</th>
                <th>Thời gian lấy xe</th>
                <th>Điểm đến</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
    `;

    const statusConfig = {
        pending: { color: "warning", label: "Chờ duyệt" },
        approved: { color: "success", label: "Đã duyệt" },
        rejected: { color: "danger", label: "Từ chối" },
        returned: { color: "info", label: "Đã trả" },
    };

    data.forEach((item, index) => {
        const vehicleName =
            [item.vehicle?.brand, item.vehicle?.license_plate]
                .filter(Boolean)
                .join(" - ") || "N/A";

        const status = statusConfig[item.status?.original || item.status] || {
            color: "secondary",
            label: "N/A",
        };

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${vehicleName}</td>
                <td>${item.created_by?.name || "N/A"}</td>
                <td>${formatDateTime(item.vehicle_pickup_time) || "N/A"}</td>
                <td>${item.destination || "N/A"}</td>
                <td class="text-center">
                    <span class="badge bg-${status.color}">${
            status.label
        }</span>
                </td>
            </tr>
        `;
    });

    html += "</tbody>";
    return html;
};

// Render bảng cảnh báo
const renderWarningTable = (data) => {
    // Xác định loại ngày hết hạn
    let dateField = "inspection_expired_at";
    let dateLabel = "Hạn đăng kiểm";

    if (data.length > 0 && data[0].liability_insurance_expired_at) {
        dateField = "liability_insurance_expired_at";
        dateLabel = "Hạn BH trách nhiệm";
    } else if (data.length > 0 && data[0].body_insurance_expired_at) {
        dateField = "body_insurance_expired_at";
        dateLabel = "Hạn BH thân vỏ";
    }

    let html = `
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 60px;">STT</th>
                <th>Hãng xe</th>
                <th>Biển số</th>
                <th>${dateLabel}</th>
                <th class="text-center">Còn lại</th>
            </tr>
        </thead>
        <tbody>
    `;

    data.forEach((item, index) => {
        const expiredDate = item[dateField];
        const daysLeft = expiredDate
            ? Math.ceil(
                  (new Date(expiredDate) - new Date()) / (1000 * 60 * 60 * 24)
              )
            : 0;
        const badgeColor =
            daysLeft <= 3 ? "danger" : daysLeft <= 7 ? "warning" : "info";

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${item.brand || "N/A"}</td>
                <td>${item.license_plate || "N/A"}</td>
                <td>${formatDateTime(expiredDate) || "N/A"}</td>
                <td class="text-center">
                    <span class="badge bg-${badgeColor}">${daysLeft} ngày</span>
                </td>
            </tr>
        `;
    });

    html += "</tbody>";
    return html;
};

// Render all charts
const renderCharts = (charts) => {
    // 1. Biểu đồ tròn - Trạng thái phương tiện (KHÔNG filter)
    if (charts.status_pie) {
        createPieChart(
            "chart-status-pie",
            charts.status_pie.labels,
            charts.status_pie.series,
            null,
            "",
            "100%",
            "100%",
            "phương tiện"
        );
    }

    // 2. Biểu đồ ngang - Top xe (KHÔNG filter - theo filter time)
    if (charts.top_vehicles) {
        createBarChart(
            "chart-top-vehicles",
            charts.top_vehicles.categories,
            charts.top_vehicles.series[0].data,
            null,
            charts.top_vehicles.series[0].name,
            "",
            null,
            null,
            "100%",
            "100%",
            "lượt",
            true // horizontal
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

    // 4. Biểu đồ kết hợp - Chi phí xăng & bảo dưỡng (CÓ filter)
    if (charts.cost_by_month) {
        createMixedChart(
            "chart-cost-by-month",
            charts.cost_by_month.categories,
            charts.cost_by_month.series[0].data,
            charts.cost_by_month.series[1].data,
            charts.cost_by_month.series[0].name,
            charts.cost_by_month.series[1].name,
            "",
            "Chi phí xăng (VNĐ)",
            "Chi phí bảo dưỡng (VNĐ)",
            "100%",
            "100%",
            "₫",
            "₫"
        );
    }

    // 5. Biểu đồ đường - Tổng km theo tháng (CÓ filter)
    if (charts.km_by_month) {
        createLineChart(
            "chart-km-by-month",
            charts.km_by_month.categories,
            charts.km_by_month.series[0].data,
            "#28a745",
            null,
            "Tổng km",
            ""
        );
    }
};

const renderExpiryTable = (containerId, data, dateField, label) => {
    const container = document.getElementById(containerId);

    if (!data || data.length === 0) {
        container.innerHTML =
            '<p class="text-muted text-center">Không có xe sắp hết hạn</p>';
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
        const vehicleName =
            [item.brand, item.license_plate].filter(Boolean).join(" - ") ||
            "N/A";

        const expiredDate = item[dateField];
        const daysLeft = expiredDate
            ? Math.ceil(
                  (new Date(expiredDate) - new Date()) / (1000 * 60 * 60 * 24)
              )
            : 0;
        const badgeColor =
            daysLeft <= 7 ? "danger" : daysLeft <= 15 ? "warning" : "info";

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>${vehicleName}</td>
                <td>${formatDateTime(expiredDate) || "N/A"}</td>
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
    });
});
