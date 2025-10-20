const statusFilter = document.getElementById("status");
const stateFilter = document.getElementById("state");
const developmentCaseFilter = document.getElementById("development-case");

window.loadList = () => {
    createDataTableServerSide(
        table,
        listUrl,
        renderColumns(),
        (item) => item,
        getFilterParams()
    );
};

const getFilterParams = () => {
    const params = {
        paginate: 1,
    };

    if (statusFilter.value) params["status"] = statusFilter.value;
    if (stateFilter.value) params["state"] = stateFilter.value;
    if (developmentCaseFilter.value)
        params["development_case"] = developmentCaseFilter.value;

    return params;
};

const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên phần mềm",
        },
        {
            data: null,
            title: "Thuộc hợp đồng",
            render: (data, type, row) => {
                return row?.contract?.name || "";
            },
        },
        {
            data: null,
            title: "Trường hợp xây dựng",
            render: (data, type, row) => {
                return row?.development_case?.converted || "";
            },
        },
        {
            data: "description",
            title: "Mô tả phần mềm",
        },
        {
            data: null,
            title: "File mô tả phần mềm",
            render: (data, type, row) => {
                return row?.attachment
                    ? createBtn(
                          "info",
                          "Xem file",
                          false,
                          {},
                          "ti ti-file-type-pdf",
                          `viewFileHandler('${row?.attachment}')`
                      )?.outerHTML
                    : "";
            },
        },
        {
            data: null,
            title: "Trạng thái phê duyệt",
            render: (data, type, row) => {
                return createBadge(row?.status?.converted, row?.status?.color);
            },
        },
        {
            data: null,
            title: "Tình trạng thực hiện",
            render: (data, type, row) => {
                return createBadge(row?.state?.converted, row?.state?.color);
            },
        },
        {
            data: null,
            title: "Người đặc tả xây dựng phần mềm",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        ${row?.business_analysts
                            ?.map(
                                (value, index) =>
                                    `<li>${value?.user?.name || ""}</li>`
                            )
                            .join("")}
                    </ul>
                `;
            },
        },
        {
            data: null,
            title: "Thành viên thực hiện xây dựng phầm mềm",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        ${row?.members
                            ?.map(
                                (value, index) =>
                                    `<li>${value?.user?.name || ""}</li>`
                            )
                            .join("")}
                    </ul>
                `;
            },
        },
        {
            data: null,
            title: "Người tạo",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Người phê duyệt",
            render: (data, type, row) => {
                return row?.verify_by?.name || "";
            },
        },
        {
            data: "rejection_reason",
            title: "Lý do từ chối",
        },
        {
            data: "rejected_at",
            title: "Thời gian từ chối",
        },
        {
            data: "accepted_at",
            title: "Thời gian chấp nhận",
        },
        {
            data: "completed_at",
            title: "Thời gian hoàn thành",
        },
        {
            data: "deadline",
            title: "Thời hạn",
        },
        {
            data: "start_date",
            title: "Ngày bắt đầu",
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
                return `
                    ${
                        row?.status?.original == "pending"
                            ? createBtn(
                                  "outline-success",
                                  "Phê duyệt",
                                  false,
                                  {},
                                  "ti ti-check",
                                  `openAcceptModal('${acceptUrl}?id=${row.id}')`
                              )?.outerHTML +
                              createBtn(
                                  "outline-danger",
                                  "Từ chối",
                                  false,
                                  {},
                                  "ti ti-x",
                                  `openRejectModal('${rejectUrl}?id=${row.id}')`
                              )?.outerHTML +
                              createEditBtn(`${editUrl}?id=${row.id}`) +
                              createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                            : ""
                    }
                    ${
                        row?.status?.original == "accepted" &&
                        row?.state?.original != "completed"
                            ? createBtn(
                                  "outline-info",
                                  "Cập nhật tình trạng",
                                  false,
                                  {},
                                  "ti ti-refresh",
                                  `openUpdateStateModal('${updateStateUrl}?id=${
                                      row.id
                                  }', ${JSON.stringify(row)})`
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
    ];
};

[statusFilter, stateFilter, developmentCaseFilter].forEach((value, index) => {
    value.addEventListener("change", () => {
        loadList();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
