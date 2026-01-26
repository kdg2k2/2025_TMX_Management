const renderColumns = () => {
    return [
        {
            data: null,
            title: "Năm",
            render: (data, type, row) => {
                return row?.year || "";
            },
        },
        {
            data: null,
            title: "Số hợp đồng",
            render: (data, type, row) => {
                return row?.contract_number || "";
            },
        },
        {
            data: null,
            width: "350px",
            title: "Tên hợp đồng",
            render: (data, type, row) => {
                return row?.name || "";
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
            data: null,
            width: "350px",
            title: "Chủ đầu tư",
            render: (data, type, row) => {
                return [
                    row?.investor?.name_vi || "",
                    row?.investor?.name_en || "",
                ]
                    .filter(Boolean)
                    .join(" - ");
            },
        },
        {
            data: null,
            title: "Giá trị hợp đồng",
            render: (data, type, row) => {
                return fmNumber(row?.contract_value) + " vnđ";
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return createBtn(
                    "info",
                    "Dữ liệu nhân sự tham gia thực hiện hợp đồng",
                    false,
                    {
                        "data-contract_id": row.id,
                    },
                    "ti ti-user-star",
                    "showPersonnelModal(this)",
                )?.outerHTML;
            },
        },
    ];
};
