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

const TABLE_CONFIGS = {
    vehicle_status: {
        columns: [
            {
                label: "STT",
                width: "60px",
                align: "center",
                render: (item, index) => index + 1,
            },
            { label: "Hãng xe", render: (item) => item.brand || "N/A" },
            { label: "Biển số", render: (item) => item.license_plate || "N/A" },
            { label: "Số km", render: (item) => fmNumber(item.current_km) },
            {
                label: "Người sử dụng",
                render: (item) => item.user?.name || "N/A",
            },
            { label: "Điểm đến", render: (item) => item.destination || "N/A" },
        ],
    },
    vehicle_loan: {
        columns: [
            {
                label: "STT",
                width: "60px",
                align: "center",
                render: (item, index) => index + 1,
            },
            {
                label: "Phương tiện",
                render: (item) =>
                    [item.vehicle?.brand, item.vehicle?.license_plate]
                        .filter(Boolean)
                        .join(" - ") || "N/A",
            },
            {
                label: "Người mượn",
                render: (item) => item.created_by?.name || "N/A",
            },
            {
                label: "Thời gian lấy xe",
                render: (item) =>
                    formatDateTime(item.vehicle_pickup_time) || "N/A",
            },
            { label: "Điểm đến", render: (item) => item.destination || "N/A" },
            {
                label: "Trạng thái",
                align: "center",
                render: (item) => {
                    const statusConfig = {
                        pending: { color: "warning", label: "Chờ duyệt" },
                        approved: { color: "success", label: "Đã duyệt" },
                        rejected: { color: "danger", label: "Từ chối" },
                        returned: { color: "info", label: "Đã trả" },
                    };
                    const status = statusConfig[
                        item.status?.original || item.status
                    ] || { color: "secondary", label: "N/A" };
                    return `<span class="badge bg-${status.color}">${status.label}</span>`;
                },
            },
        ],
    },
    warning: {
        columns: [],
    },
};

const WARNING_CONFIG = {
    inspection: {
        field: "inspection_expired_at",
        label: "Hạn đăng kiểm",
    },
    liability_insurance: {
        field: "liability_insurance_expired_at",
        label: "Hạn BH trách nhiệm",
    },
    body_insurance: {
        field: "body_insurance_expired_at",
        label: "Hạn BH thân vỏ",
    },
};

const getWarningTableConfig = (warningType) => {
    const warningInfo = WARNING_CONFIG[warningType];
    if (!warningInfo) {
        console.error("Unknown warning type:", warningType);
        return null;
    }

    return {
        columns: [
            {
                label: "STT",
                width: "60px",
                align: "center",
                render: (item, index) => index + 1,
            },
            { label: "Hãng xe", render: (item) => item.brand || "N/A" },
            { label: "Biển số", render: (item) => item.license_plate || "N/A" },
            {
                label: warningInfo.label,
                render: (item) =>
                    formatDateTime(item[warningInfo.field]) || "N/A",
            },
            {
                label: "Còn lại",
                align: "center",
                render: (item) => {
                    const expiredDate = item[warningInfo.field];
                    if (!expiredDate)
                        return '<span class="badge bg-secondary">N/A</span>';

                    const daysLeft = Math.ceil(
                        (new Date(expiredDate) - new Date()) /
                            (1000 * 60 * 60 * 24)
                    );
                    const badgeColor =
                        daysLeft <= 3
                            ? "danger"
                            : daysLeft <= 7
                            ? "warning"
                            : "info";
                    return `<span class="badge bg-${badgeColor}">${daysLeft} ngày</span>`;
                },
            },
        ],
    };
};

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
            );

        if (res.data.counter_activity)
            renderStatsCards(
                res.data.counter_activity,
                statsContainerActivity,
                "col-md-6 col-xl-4"
            );

        if (res.data.charts) renderCharts(res.data.charts);
    }
};

const renderStatsCards = (
    counter,
    container,
    classSize = "col-md-6 col-xl-3"
) => {
    container.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = classSize + " mb-3";

        const hasDetail = element?.value > 0;
        const cursorStyle = hasDetail ? "cursor: pointer;" : "";
        const clickAttr = hasDetail
            ? `data-detail-key="${element.detail_key}"
               data-detail-filter="${element.detail_filter}"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="Click để xem chi tiết"`
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

        if (element?.value > 0) {
            const card = colDiv.querySelector(".stat-card");
            card.addEventListener("click", () => openStatDetailModal(element));
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

    modalTitle.textContent = element.converted;

    modalContent.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    modal.show();

    try {
        const params = {
            ...currentFilterParams,
            type: element.detail_key,
            filter: element.detail_filter,
        };

        const res = await http.get(apiVehicleStatisticDetail, params);

        if (res?.data) {
            renderDetailTable(
                modalContent,
                res.data,
                element.detail_key,
                element.detail_filter
            );
        }
    } catch (error) {
        modalContent.innerHTML =
            '<p class="text-danger text-center">Có lỗi xảy ra khi tải dữ liệu</p>';
    }
};

const renderDetailTable = (container, data, type, filter = null) => {
    if (!data || data.length === 0) {
        container.innerHTML =
            '<p class="text-muted text-center">Không có dữ liệu</p>';
        return;
    }

    let config;
    if (type === "warning") {
        config = getWarningTableConfig(filter);
        if (!config) {
            container.innerHTML =
                '<p class="text-danger text-center">Loại cảnh báo không hợp lệ</p>';
            return;
        }
    } else {
        config = TABLE_CONFIGS[type];
        if (!config) {
            container.innerHTML =
                '<p class="text-danger text-center">Không tìm thấy cấu hình bảng</p>';
            return;
        }
    }

    let html = '<table class="table table-hover" id="detail-table">';

    // Header
    html += "<thead><tr>";
    config.columns.forEach((col) => {
        const width = col.width ? ` style="width: ${col.width};"` : "";
        const align = col.align ? ` class="text-${col.align}"` : "";
        html += `<th${width}${align}>${col.label || ""}</th>`;
    });
    html += "</tr></thead>";

    // Body
    html += "<tbody>";
    data.forEach((item, index) => {
        html += "<tr>";
        config.columns.forEach((col) => {
            const align = col.align ? ` class="text-${col.align}"` : "";
            const content = col.render(item, index);
            html += `<td${align}>${content}</td>`;
        });
        html += "</tr>";
    });
    html += "</tbody>";

    html += "</table>";
    container.innerHTML = html;

    // Initialize DataTable
    initDataTable($("#detail-table"));
};

const renderCharts = (charts) => {
    const chartRenderers = {
        top_vehicles: () =>
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
                true
            ),
        loan_by_month: () =>
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
            ),
        cost_by_month: () =>
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
            ),
        km_by_month: () =>
            createLineChart(
                "chart-km-by-month",
                charts.km_by_month.categories,
                charts.km_by_month.series[0].data,
                "#28a745",
                null,
                "Tổng km",
                ""
            ),
    };

    Object.entries(chartRenderers).forEach(([key, renderer]) => {
        if (charts[key]) renderer();
    });
};

const resetFilter = () => {
    document.getElementById("filter-year").value = new Date().getFullYear();
    document.getElementById("filter-month").value = "";
    loadData();
};

document.addEventListener("DOMContentLoaded", async () => {
    await loadData();

    document
        .getElementById("btn-filter")
        ?.addEventListener("click", async () => {
            await loadData();
        });

    document.getElementById("btn-reset")?.addEventListener("click", () => {
        resetFilter();
    });
});
