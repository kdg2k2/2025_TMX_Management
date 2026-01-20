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
            title: "Tên",
            render: (data, type, row) => {
                return row?.name || "";
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
                    .filter(Boolean)
                    .join(" - ");
            },
        },
        {
            data: null,
            title: "PT chuyên môn",
            render: (data, type, row) => {
                return (
                    row?.professionals
                        ?.map((item) => item?.user?.name)
                        .filter(Boolean)
                        .join(", ") || ""
                );
            },
        },
        {
            data: null,
            title: "Link GG Drive",
            render: (data, type, row) => {
                return row?.ggdrive_link ? createViewBtn(row.ggdrive_link) : "";
            },
        },
        {
            data: null,
            title: "Nhân sự thực hiện",
            render: (data, type, row) => {
                return `
                    <ul>
                        <li>
                            <b>
                                Người hoàn thiện
                            </b>
                            ${row?.executor_user?.name || ""}
                        </li>
                        <li>
                            <b>
                                Người hoàn thiện
                            </b>
                            ${row?.inspector_user?.name || ""}
                        </li>
                        <li>
                            <b>
                                Người hoàn thiện
                            </b>
                            ${
                                row?.intermediate_collaborators
                                    ?.map((item) => item?.user?.name)
                                    .filter(Boolean)
                                    .join(", ") || ""
                            }
                        </li>
                    </ul>
                `;
            },
        },
        createCreatedUpdatedColumn(),
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                const contractParam = `?contract_id=${row.id}`;
                return `
                    ${
                        createActionBtn(
                            "success",
                            "Sản phẩm chính",
                            "",
                            "loadList",
                            null,
                            "ti ti-package",
                        ) +
                        createActionBtn(
                            "info",
                            "Sản phẩm trung gian",
                            "",
                            "loadList",
                            null,
                            "ti ti-packages",
                        ) +
                        createActionBtn(
                            "warning",
                            "Yêu cầu kiểm tra",
                            "",
                            "loadList",
                            null,
                            "ti ti-clipboard-search",
                        ) +
                        createActionBtn(
                            "secondary",
                            "Phản hồi kiểm tra",
                            "",
                            "loadList",
                            null,
                            "ti ti-clipboard-check",
                        ) +
                        createActionBtn(
                            "primary",
                            "Biên bản",
                            "",
                            "loadList",
                            null,
                            "ti ti-file-text",
                        ) +
                        createApproveBtn(
                            contractProductMinuteApprove + contractParam,
                        ) +
                        createRejectBtn(
                            contractProductMinuteReject + contractParam,
                        )
                    }
                `;
            },
        },
    ];
};
