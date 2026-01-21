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
            title: "Tên hợp đồng",
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
                                Người hoàn thiện:
                            </b>
                            ${row?.executor_user?.name || ""}
                        </li>
                        <li>
                            <b>
                                Người hoàn thiện:
                            </b>
                            ${row?.inspector_user?.name || ""}
                        </li>
                        <li>
                            <b>
                                Người phối hợp làm SPTG:
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
                            apiContractProductMainList,
                            null,
                            "showProductModal",
                            "ti ti-package",
                            {
                                "data-type": "main",
                                "data-contract_id": row.id,
                            },
                        ) +
                        createActionBtn(
                            "info",
                            "Sản phẩm trung gian",
                            apiContractProductIntermediateList,
                            null,
                            "showProductModal",
                            "ti ti-packages",
                            {
                                "data-type": "intermediate",
                                "data-contract_id": row.id,
                            },
                        )
                    }
                    ${
                        !row.inspection_status ||
                        row.inspection_status != "request"
                            ? createActionBtn(
                                  "warning",
                                  "Yêu cầu kiểm tra",
                                  apiContractProductInspectionRequest +
                                      contractParam,
                                  "loadList",
                                  "showRequestInspectionProductModal",
                                  "ti ti-clipboard-search",
                                  {
                                      "data-contract_id": row.id,
                                  },
                              )
                            : row.is_auth_inspector
                              ? createActionBtn(
                                    "danger",
                                    "Hủy yêu cầu kiểm tra",
                                    `${apiContractProductInspectionCancel}?id=${row.inspection_id}`,
                                    "loadList",
                                    "showCancelInspectionProductModal",
                                    "ti ti-ban",
                                ) +
                                createActionBtn(
                                    "secondary",
                                    "Phản hồi kiểm tra",
                                    `${apiContractProductInspectionResponse}?id=${row.inspection_id}`,
                                    "loadList",
                                    "showResponseInspectionProductModal",
                                    "ti ti-clipboard-check",
                                )
                              : ""
                    }
                    ${
                        row.inspection_status == "responded" &&
                        row.is_inspection_created_by_auth
                            ? createActionBtn(
                                  "primary",
                                  "Biên bản",
                                  "",
                                  "loadList",
                                  null,
                                  "ti ti-file-text",
                              )
                            : ""
                    }
                    ${
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

const callbackAfterRenderLoadList = () => {
    const table = ($("#datatable") || table).DataTable();
    table.rows().every(function () {
        const data = this.data();
        const rowNode = this.node();

        if (data?.product_minutes?.length > 0) {
            const record = data?.product_minutes?.slice(-1)[0];
            $(rowNode).addClass(`text-${record.status.color}`);
            $(rowNode).attr("title", record.status.converted);
        }
    });
};
