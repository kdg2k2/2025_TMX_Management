const statsContainerCurrent = document.getElementById(
    "stats-container-current"
);
const statsContainerActivity = document.getElementById(
    "stats-container-activity"
);
const loadData = async () => {
    const year =
        document.getElementById("filter-year")?.value ||
        new Date().getFullYear();
    const month = document.getElementById("filter-month")?.value || "";

    const params = { year };
    if (month) params.month = month;

    const res = await http.get(apiVehicleStatisticData, params);

    if (res?.data) {
        if (res.data.counter_current_status)
            renderStatsCards(
                res.data.counter_current_status,
                statsContainerCurrent
            );

        if (res.data.counter_activity)
            renderStatsCards(res.data.counter_activity, statsContainerActivity);

        if (res.data.charts) renderCharts(res.data.charts);

        if (res.data.loan_returned_not_ready_detail)
            renderReturnedNotReadyTable(
                res.data.loan_returned_not_ready_detail
            );
    }
};
const renderStatsCards = (
    counter,
    container,
    classSize = "col-md-6 col-xl-4"
) => {
    container.innerHTML = "";

    counter.forEach((element) => {
        const colDiv = document.createElement("div");
        colDiv.className = classSize;

        colDiv.innerHTML = `
            <div class="card custom-card dashboard-main-card overflow-hidden ${
                element?.color
            }">
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

        container.appendChild(colDiv);
    });
};
const renderCharts = (charts) => {
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
};
const renderReturnedNotReadyTable = (data) => {
    const container = document.getElementById("table-returned-not-ready");

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
        unwashed: { color: "purple", label: "Chưa rửa", icon: "ti ti-car-crash" },
        broken: { color: "danger", label: "Hỏng", icon: "ti ti-x" },
        faulty: { color: "warning", label: "Lỗi", icon: "ti ti-alert-circle" },
        lost: { color: "dark", label: "Thất lạc", icon: "ti ti-help" },
    };

    data.forEach((item, index) => {
        const status = statusConfig[item.vehicle_status_return] || {
            color: "secondary",
            label: item.vehicle_status_return,
            icon: "ti ti-circle",
        };

        html += `
            <tr>
                <td class="text-center">${index + 1}</td>
                <td>
                    ${[item.vehicle?.brand, item.vehicle?.license_plate].filter(Boolean).join(" - ")}
                </td>
                <td>${item.created_by?.name || "N/A"}</td>
                <td>${formatDateTime(item.vehicle_pickup_time) || "N/A"}</td>
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
        refreshSumoSelect();
    });
});
