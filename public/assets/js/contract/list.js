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
                return row?.type?.name;
            },
        },
        {
            data: null,
            title: "Chủ đầu tư",
            render: (data, type, row) => {
                return [row?.investor?.name_vi, row?.investor?.name_en]
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
            data: "end_date",
            title: "Ngày kêt thúc",
        },
        {
            data: null,
            title: "Ngày kết thúc gia hạn",
            render: (data, type, row) => {
                return formatDateToYmd(
                    row?.extensions?.at(-1)?.new_end_date || ""
                );
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
        ${
            row.path_file_full
                ? createBtn(
                      "info",
                      "Xem file full",
                      false,
                      {},
                      "ti ti-file-type-pdf",
                      `window.open(${row.path_file_full}, '_blank')`
                  )?.outerHTML
                : ""
        }
        ${
            row.path_file_short
                ? createBtn(
                      "info",
                      "Xem file short",
                      false,
                      {},
                      "ti ti-file-type-pdf",
                      `window.open(${row.path_file_short}, '_blank')`
                  )?.outerHTML
                : ""
        }
    `;
};
