const gerenalInfoContainer = document.getElementById("gerenal-info");

const listRenderGerenal = [
    {
        title: "Phụ trách chuyên môn",
        content: (item) => renderMultipleGerenaInfo(item?.professionals || []),
    },
    {
        title: "Phụ trách giải ngân",
        content: (item) => renderMultipleGerenaInfo(item?.disbursements || []),
    },
    {
        title: "Hướng dẫn",
        content: (item) => renderMultipleGerenaInfo(item?.instructors || []),
    },
    {
        title: "Đầu mối kế toán",
        content: (item) => renderSingleGerenaInfo(item?.accounting_contact),
    },
    {
        title: "Người kiểm tra SP",
        content: (item) => renderSingleGerenaInfo(item?.inspector_user),
    },
    {
        title: "Người thực hiện SP",
        content: (item) => renderSingleGerenaInfo(item?.executor_user),
    },
    {
        title: "Người hỗ trợ thực hiện SP",
        content: (item) =>
            renderMultipleGerenaInfo(item?.intermediate_collaborators || []),
    },
    {
        title: "Địa điểm",
        content: (item) =>
            renderMultipleGerenaInfo(item?.scopes || [], "province"),
    },
    {
        title: "Giá trị HĐ",
        content: (item) => fmNumber(item?.contract_value) + "vnd",
    },
    {
        title: "Mức thuế",
        content: (item) => item?.vat_rate + "%",
    },
    {
        title: "VAT",
        content: (item) => fmNumber(item?.vat_amount) + "vnd",
    },
    {
        title: "Ngày kết thúc",
        content: (item) => formatDateToYmd(item?.end_date),
    },
    {
        title: "Ngày kết thúc gia hạn",
        content: (item) =>
            formatDateToYmd(item?.extensions?.at(-1)?.new_end_date || ""),
    },
];

const renderMultipleGerenaInfo = (
    array,
    multipleKey = "user",
    singleKey = "name"
) => {
    if (!array || array.length === 0) {
        return '<span class="text-muted">Chưa có dữ liệu</span>';
    }

    return `
        <ul class="m-0 ps-3">
            ${array
                .map(
                    (value) =>
                        `<li>${renderSingleGerenaInfo(
                            value[multipleKey] ?? [],
                            singleKey
                        )}</li>`
                )
                .join("")}
        </ul>
    `;
};

const renderSingleGerenaInfo = (item, key = "name") => {
    if (!item || !item[key]) {
        return '<span class="text-muted">Chưa có dữ liệu</span>';
    }
    return item[key];
};

const renderGerenaInfo = () => {
    const html = listRenderGerenal
        .map(
            (value) => `
            <div class="col-lg-3 col-md-6 mb-3">
                <label class="fw-bold d-block">${value.title}:</label>
                <div class="mb-0">${value.content(contractDetail)}</div>
            </div>
        `
        )
        .join("");

    gerenalInfoContainer.innerHTML = html;
};
