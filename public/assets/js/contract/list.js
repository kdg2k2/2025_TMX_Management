const renderColumns = () => {
    return [
        {
            data: "year",
            title: "Năm",
        },
        {
            data: null,
            title: "Số hợp đồng",
            render: (data, type, row) => {
                return createBtn(
                    "outline-primary",
                    "Chi tiết hợp đồng",
                    true,
                    {
                        "data-bs-target": `#${contractDetailModal.getAttribute(
                            "id"
                        )}`,
                        "data-bs-toggle": "modal",
                        "data-id": row.id,
                        "data-onsuccess": "loadContractDetail",
                    },
                    "",
                    null,
                    row.contract_number
                )?.outerHTML;
            },
        },
        {
            data: "name",
            title: "Tên",
        },
        {
            data: "short_name",
            title: "Tên viết tắt",
        },
        {
            data: null,
            title: "Loại HĐ",
            render: (data, type, row) => {
                return row?.type?.name || "";
            },
        },
        {
            data: null,
            title: "Chủ đầu tư",
            render: (data, type, row) => {
                return [
                    row?.investor?.name_vi || "",
                    row?.investor?.name_en || "",
                ]
                    .filter((v) => v != null && v !== "")
                    .join(" - ");
            },
        },
        {
            data: null,
            title: "Địa điểm",
            render: (data, type, row) => {
                return row?.scopes
                    ?.map((value, index) => value?.province?.name)
                    .join(", ");
            },
        },
        {
            data: "signed_date",
            title: "Ngày ký",
        },
        {
            data: null,
            title: "Ngày kêt thúc",
            render: (data, type, row) => {
                return row?.end_date || "";
            },
        },
        {
            data: null,
            title: "Ngày kết thúc gia hạn",
            render: (data, type, row) => {
                const appendix = row?.appendixes[0] || [];
                const date = appendix?.renewal_end_date || "";
                return date
                    ? date + ` (theo phụ lục HĐ lần ${appendix?.times})`
                    : "";
            },
        },
        {
            data: null,
            title: "Mức thuế",
            render: (data, type, row) => {
                return row?.vat_rate + "%";
            },
        },
        {
            data: null,
            title: "Giá trị HĐ",
            render: (data, type, row) => {
                return fmNumber(row?.contract_value) + " vnđ";
            },
        },
        {
            data: null,
            title: "Trạng thái",
            render: (data, type, row) => {
                return row?.contract_status?.converted;
            },
        },
        {
            data: null,
            title: "Thời gian tạo/cập nhật",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        <li>${row.created_at}</li>
                        <li>${row.updated_at}</li>
                    </ul>
                `;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return renderActionButtons(row);
            },
        },
    ];
};

const renderActionButtons = (row) => {
    return `
        ${createEditBtn(`${editUrl}?id=${row.id}`)}
        ${createDeleteBtn(`${deleteUrl}?id=${row.id}`)}
        ${row.path_file_full ? createViewBtn(row.path_file_full) : ""}
        ${row.path_file_short ? createViewBtn(row.path_file_short) : ""}
    `;
};

document
    .getElementById("export-contract")
    .addEventListener("click", async () => {
        const res = await http.get(exportUrl);
        downloadFileHandler(res?.data);
    });
