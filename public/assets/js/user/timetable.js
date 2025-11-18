const year = document.getElementById("year");
const week = document.getElementById("week");
const department = document.getElementById("department");
const timetableContainer = document.getElementById("timetable-container");
const modalWarning = document.getElementById("modal-warning");
const modalWarningForm = modalWarning.querySelector("form");

let currentDataTables = []; // Lưu các DataTable instances

const getAndFillWeekSelect = async () => {
    const res = await http.get(apiUserTimetableGetWeeks, {
        year: year.value,
    });

    if (res.data) {
        fillSelectElement(week, res.data, "week_number", "label", "", false);

        // Tìm tuần hiện tại và select nó
        const currentWeek = res.data.find((w) => w.is_current);
        if (currentWeek) {
            week.value = currentWeek.week_number;
        }

        loadDataByWeek();
    }
};

window.loadDataByWeek = async () => {
    // Destroy tất cả DataTable cũ
    destroyAllDataTables();

    const res = await http.get(apiUserTimetableList, {
        year: year.value,
        week: week.value,
        department_id: department.value,
    });

    if (res.data) {
        renderTimetable(res.data);
    }
};

const destroyAllDataTables = () => {
    currentDataTables.forEach((dt) => {
        if (dt && $.fn.DataTable.isDataTable(dt)) {
            $(dt).DataTable().destroy();
        }
    });
    currentDataTables = [];
};

const renderTimetable = (days) => {
    // Lấy ngày hôm nay để so sánh
    const today = new Date().toISOString().split("T")[0]; // Format: YYYY-MM-DD

    // Tìm index của ngày hôm nay trong danh sách
    let activeDayIndex = days.findIndex((day) => day.date === today);

    // Nếu không tìm thấy ngày hôm nay (khác tuần), mặc định tab đầu tiên
    if (activeDayIndex === -1) {
        activeDayIndex = 0;
    }

    // Tạo tabs từ dữ liệu
    const tabs = days.map((day, index) => {
        const isToday = day.date === today;
        return {
            title: `${day.day_name} ${formatDateTime(day.date)}`,
            icon: "ri-calendar-line",
            content: renderDayContent(day, index),
            isToday: isToday,
        };
    });

    // Render nav-tabs structure
    const tabsHtml = `
        <div>
            <ul class="nav nav-tabs nav-style-2 border-bottom" role="tablist">
                ${tabs
                    .map(
                        (tab, index) => `
                    <li class="nav-item" role="presentation">
                        <a class="nav-link ${
                            index === activeDayIndex ? "active" : ""
                        }"
                            data-bs-toggle="tab"
                            role="tab"
                            href="#day-tab-${index}"
                            aria-selected="${
                                index === activeDayIndex ? "true" : "false"
                            }">
                            <i class="${tab.icon} me-1"></i>
                            ${tab.title}
                            ${
                                tab.isToday
                                    ? '<span class="badge bg-primary ms-1">Hôm nay</span>'
                                    : ""
                            }
                        </a>
                    </li>
                `
                    )
                    .join("")}
            </ul>
            <div class="tab-content mt-3">
                ${tabs
                    .map(
                        (tab, index) => `
                    <div class="tab-pane w-100 ${
                        index === activeDayIndex ? "active show" : ""
                    }"
                        id="day-tab-${index}"
                        role="tabpanel">
                        ${tab.content}
                    </div>
                `
                    )
                    .join("")}
            </div>
        </div>
    `;

    timetableContainer.innerHTML = tabsHtml;

    // Khởi tạo DataTable cho từng bảng
    days.forEach((day, index) => {
        const tableElement = $(`#table-day-${index}`);
        if (tableElement.length > 0) {
            const dataTable = initDataTable(tableElement);
            currentDataTables.push(tableElement);
        }
    });
};

const renderDayContent = (day, dayIndex) => {
    if (!day.users || day.users.length === 0) {
        return '<div class="alert alert-info">Không có dữ liệu</div>';
    }

    return `
        <div class="table-responsive">
            <table id="table-day-${dayIndex}" class="table table-bordered table-hover nowrap w-100">
                <thead>
                    <tr>
                        <th>Họ tên</th>
                        <th>Trạng thái</th>
                        <th>Phòng ban</th>
                        <th>Chức vụ</th>
                        <th>Chức danh</th>
                        <th>Chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    ${day.users
                        .map(
                            (user, index) => `
                        <tr>
                            <td>
                                    <img src="${user.path}" alt="${
                                user.user_name
                            }" class="rounded-circle" width="40" height="40" onerror="this.src='assets/images/brand-logos/toggle-logo.png'">
                                ${user.user_name}
                            </td>
                            <td>
                                ${createBadge(
                                    user.status.converted,
                                    user.status.color
                                )}
                            </td>
                            <td>${user.department || "-"}</td>
                            <td>${user.position || "-"}</td>
                            <td>${user.job_title || "-"}</td>
                            <td>${renderUserDetails(user)}</td>
                            <td>${renderActions(user, day.date)}</td>
                        </tr>
                    `
                        )
                        .join("")}
                </tbody>
            </table>
        </div>
    `;
};

const renderUserDetails = (user) => {
    if (!user.details) {
        return "-";
    }

    if (user.details.type === "work_schedule") {
        return `
            <div class="small">
                <strong>Địa điểm:</strong> ${user.details.detail.address}<br>
                <strong>Nội dung:</strong> ${user.details.detail.content}<br>
                <strong>Thời gian:</strong> ${user.details.detail.from_date} - ${user.details.detail.to_date}
            </div>
        `;
    }

    if (user.details.type === "leave_request") {
        return `
            <div class="small">
                <strong>Lý do:</strong> ${user.details.detail.reason}<br>
                <strong>Kiểu đăng ký:</strong> ${user.details.detail.type.converted}<br>
                <strong>Thời gian:</strong> ${user.details.detail.from_date} - ${user.details.detail.to_date}
            </div>
        `;
    }

    return "-";
};

const renderActions = (user, date) => {
    return `
        ${
            user?.warning != null
                ? "Đã bị cảnh báo"
                : `
                    <div class="text-center">
                        ${
                            user?.details?.type == "work_schedule"
                                ? createBtn(
                                      "outline-warning",
                                      "Cảnh báo công tác",
                                      false,
                                      {
                                          "data-href": `${apiUserWarningStore}?user_id=${user?.user_id}&warning_date=${date}&type=work_schedule&work_schedule_id=${user?.details?.detail?.id}`,
                                          "data-onsuccess": "loadDataByWeek",
                                      },
                                      "ti ti-calendar-exclamation",
                                      "openModalWarning(this)"
                                  )?.outerHTML
                                : ""
                        }
                        ${
                            createBtn(
                                "outline-secondary",
                                "Cảnh báo công việc",
                                false,
                                {
                                    "data-href": `${apiUserWarningStore}?user_id=${user?.user_id}&warning_date=${date}&type=job`,
                                    "data-onsuccess": "loadDataByWeek",
                                },
                                "ti ti-alert-triangle",
                                "openModalWarning(this)"
                            )?.outerHTML
                        }
                    </div>
            `
        }
    `;
};

const openModalWarning = (btn) => {
    openModalBase(btn, {
        modal: modalWarning,
        form: modalWarningForm,
    });
};

modalWarningForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalWarningForm, () => {
        hideModal(modalWarning);
    });
});

year.addEventListener("change", getAndFillWeekSelect);
[week, department].forEach((item) =>
    item.addEventListener("change", loadDataByWeek)
);

document.addEventListener("DOMContentLoaded", loadDataByWeek);
