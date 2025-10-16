const gerenalInfoContainer = document.getElementById("gerenal-info");

// Phân nhóm thông tin theo category
const infoGroups = {
    general: {
        title: "Thông tin chung",
        icon: "ti ti-file-info",
        color: "info",
        items: ["name", "short_name", "contract_number", "year", "scopes"],
    },
    personnel: {
        title: "Nhân sự phụ trách",
        icon: "ti ti-users",
        color: "primary",
        items: [
            "professionals",
            "disbursements",
            "instructors",
            "accounting_contact",
        ],
    },
    execution: {
        title: "Thực hiện sản phẩm",
        icon: "ti ti-clipboard-check",
        color: "success",
        items: [
            "inspector_user",
            "executor_user",
            "intermediate_collaborators",
        ],
    },
    financial: {
        title: "Thông tin tài chính",
        icon: "ti ti-currency-dollar",
        color: "warning",
        items: [
            "contract_value",
            "vat_rate",
            "vat_amount",
            "acceptance_value",
            "liquidation_value",
        ],
    },
    timeline: {
        title: "Thời gian",
        icon: "ti ti-calendar",
        color: "danger",
        items: [
            "signed_date",
            "effective_date",
            "end_date",
            "renewal_end_date",
            "completion_date",
            "acceptance_date",
            "liquidation_date",
        ],
    },
};

const fieldMapping = {
    professionals: {
        label: "Phụ trách chuyên môn",
        render: (item) => renderMultipleGerenaInfo(item?.professionals || []),
    },
    disbursements: {
        label: "Phụ trách giải ngân",
        render: (item) => renderMultipleGerenaInfo(item?.disbursements || []),
    },
    instructors: {
        label: "Hướng dẫn",
        render: (item) => renderMultipleGerenaInfo(item?.instructors || []),
    },
    accounting_contact: {
        label: "Đầu mối kế toán",
        render: (item) =>
            renderMultipleGerenaInfo([item?.accounting_contact] || []),
    },
    inspector_user: {
        label: "Người kiểm tra SP",
        render: (item) =>
            renderMultipleGerenaInfo([item?.inspector_user] || []),
    },
    executor_user: {
        label: "Người thực hiện SP",
        render: (item) => renderMultipleGerenaInfo([item?.executor_user] || []),
    },
    intermediate_collaborators: {
        label: "Người hỗ trợ thực hiện",
        render: (item) =>
            renderMultipleGerenaInfo(item?.intermediate_collaborators || []),
    },
    scopes: {
        label: "Khu vực",
        render: (item) => renderLocationBadges(item?.scopes || []),
    },
    contract_value: {
        label: "Giá trị hợp đồng",
        render: (item) =>
            renderFinancialInfo(
                item?.contract_value,
                "text-primary fw-bold fs-5"
            ),
    },
    vat_rate: {
        label: "Mức thuế VAT",
        render: (item) => createBadge(`${item?.vat_rate || 0}%`, "secondary"),
    },
    vat_amount: {
        label: "Số tiền VAT",
        render: (item) => renderFinancialInfo(item?.vat_amount),
    },
    acceptance_value: {
        label: "Giá trị nghiệm thu",
        render: (item) =>
            renderFinancialInfo(item?.acceptance_value, "fw-bold fs-5"),
    },
    liquidation_value: {
        label: "Giá trị thanh lý",
        render: (item) =>
            renderFinancialInfo(item?.liquidation_value, "fw-bold fs-5"),
    },
    signed_date: {
        label: "Ngày ký hợp đồng",
        render: (item) => createDateBadge(item?.signed_date, "primary"),
    },
    effective_date: {
        label: "Ngày hợp đồng có hiệu lực",
        render: (item) => createDateBadge(item?.effective_date, "success"),
    },
    end_date: {
        label: "Ngày kết thúc hợp đồng",
        render: (item) => createDateBadge(item?.end_date, "warning"),
    },
    renewal_end_date: {
        label: "Ngày kết thúc gia hạn",
        render: (item) => {
            const appendix = item?.appendixes[0] || [];
            const date = formatDateTime(appendix?.renewal_end_date || "");
            return createDateBadge(
                date ? date + ` (theo phụ lục HĐ lần ${appendix?.times})` : "",
                "danger"
            );
        },
    },
    completion_date: {
        label: "Ngày hoàn thành",
        render: (item) => createDateBadge(item?.completion_date, "success"),
    },
    acceptance_date: {
        label: "Ngày nghiệm thu hợp đồng",
        render: (item) => createDateBadge(item?.acceptance_date, "primary"),
    },
    liquidation_date: {
        label: "Ngày thanh lý hợp đồng",
        render: (item) => createDateBadge(item?.liquidation_date, "primary"),
    },
    name: {
        label: "Tên hợp đồng",
        render: (item) => item?.name,
    },
    short_name: {
        label: "Tên hợp đồng viết tắt",
        render: (item) => item?.short_name,
    },
    contract_number: {
        label: "Số hợp đồng",
        render: (item) => createBadge(item?.contract_number, "outline-primary"),
    },
    year: {
        label: "Năm",
        render: (item) => item?.year,
    },
};

const renderMultipleGerenaInfo = (
    array,
    multipleKey = "user",
    singleKey = "name"
) => {
    if (!array || array.length === 0) {
        return '<span class="text-muted fst-italic small">Chưa cập nhật</span>';
    }

    return `
        <div class="d-flex flex-wrap gap-2">
            ${array
                .map((value) =>
                    createBadge(
                        value[multipleKey]?.[singleKey] ||
                            value[singleKey] ||
                            "N/A",
                        "outline-light border",
                        "ti ti-user-circle",
                        "dark"
                    )
                )
                .join("")}
        </div>
    `;
};

const renderLocationBadges = (locations) => {
    if (!locations || locations.length === 0) {
        return '<span class="text-muted fst-italic small">Chưa cập nhật</span>';
    }

    return `
        <div class="d-flex flex-wrap gap-2">
            ${locations
                .map((loc) =>
                    createBadge(loc.province?.name, "info", "ti ti-map-pin")
                )
                .join("")}
        </div>
    `;
};

const renderFinancialInfo = (value, className = "") => {
    if (!value && value !== 0)
        return '<span class="text-muted fst-italic small">Chưa cập nhật</span>';

    return `
        <div class="${className}">
            ${fmNumber(value)} <small class="text-muted">VND</small>
        </div>
    `;
};

const createDateBadge = (date, color = "secondary") => {
    if (!date)
        return '<span class="text-muted fst-italic small">Chưa cập nhật</span>';

    return createBadge(date, `outline-${color}`, "ti ti-calendar", true);
};

const renderGerenaInfo = () => {
    let html = '<div class="row g-4 m-0">';

    Object.entries(infoGroups).forEach(([key, group]) => {
        const isFinancialOrTimeline = key === "financial" || key === "timeline";

        html += `
            <div class="${
                isFinancialOrTimeline ? "col-lg-6" : "col-lg-4"
            } my-1">
                <div class="card h-100 border-top border-3 border-${
                    group.color
                }">
                    <div class="card-header bg-light-${group.color} py-2">
                        <h6 class="mb-0 d-flex align-items-center gap-2">
                            <i class="${group.icon} text-${group.color}"></i>
                            <span>${group.title}</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex ${
                            isFinancialOrTimeline
                                ? "flex-column flex-lg-row flex-wrap"
                                : "flex-column"
                        } gap-3">
                            ${group.items
                                .map((itemKey) => {
                                    const field = fieldMapping[itemKey];
                                    if (!field) return "";

                                    return `
                                        <div>
                                            <label class="form-label text-muted small mb-1">
                                                ${field.label}
                                            </label>
                                            <div>
                                                ${field.render(contractDetail)}
                                            </div>
                                        </div>
                                    `;
                                })
                                .join("")}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    html += "</div>";

    gerenalInfoContainer.innerHTML = html;
};
