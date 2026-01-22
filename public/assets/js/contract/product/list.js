const renderColumns = () => {
    return [
        {
            data: null,
            width: "600px",
            title: "Hợp đồng",
            render: (data, type, row) => {
                const year = row?.year;
                const contractNumber = row?.contract_number;
                const contractName = row?.name;
                const investor = [
                    row?.investor?.name_vi,
                    row?.investor?.name_en,
                ]
                    .filter(Boolean)
                    .join(" - ");

                if (type === "sort" || type === "filter") {
                    return [year, contractNumber, contractName, investor]
                        .filter(Boolean)
                        .join(" ");
                }

                return `
                    <div class="text-primary fw-semibold">
                        <i class="ti ti-calendar me-1"></i>${year ?? ""}
                    </div>

                    <div class="fw-semibold">
                        <i class="ti ti-file-text me-1"></i>${contractNumber ?? ""}
                    </div>

                    <div>
                        <i class="ti ti-writing me-1"></i>${contractName ?? ""}
                    </div>

                    <div class="text-muted small">
                        <i class="ti ti-building me-1"></i>${investor}
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Link GG Drive",
            render: (data, type, row) => {
                return row?.ggdrive_link
                    ? `<div class="text-center">${createViewBtn(row?.ggdrive_link)}</div>`
                    : "";
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
                                PT chuyên môn:
                            </b>
                            ${
                                row?.professionals
                                    ?.map((item) => item?.user?.name)
                                    .filter(Boolean)
                                    .join(", ") || ""
                            }
                        </li>
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
                                Phối hợp làm SPTG:
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
                        createActionBtn(
                            "warning",
                            "Kiểm tra sản phẩm",
                            apiContractProductInspectionList,
                            "loadList",
                            "showInspectionProductModal",
                            "ti ti-clipboard-search",
                            {
                                "data-contract_id": row.id,
                            },
                        ) +
                        createActionBtn(
                            "primary",
                            "Biên bản",
                            apiContractProductMinuteList,
                            "loadList",
                            "showMinuteProductModal",
                            "ti ti-file-text",
                            {
                                "data-contract_id": row.id,
                                "data-contract_number": row.contract_number,
                                "data-contract_signed_date": row.signed_date,
                            },
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
