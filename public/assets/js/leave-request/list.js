const createdBy = document.getElementById("created-by");
const approvalStatus = document.getElementById("approval-status");
const adjustApprovalStatus = document.getElementById("adjust-approval-status");
const fromDateFilter = document.getElementById("from-date-filter");
const toDateFilter = document.getElementById("to-date-filter");

const renderColumns = () => {
    return [
        {
            data: null,
            title: "Người tạo",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Lý do",
            render: (data, type, row) => {
                return row?.reason || "";
            },
        },
        {
            data: null,
            title: "Thời gian",
            render: (data, type, row) => {
                return `
                    <div class="text-center">
                        ${row?.from_date}
                        <br>
                        ${row?.to_date}
                        <br>
                        ${row?.total_leave_days} ngày
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Trạng thái phê duyệt",
            render: (data, type, row) => {
                return createBadge(
                    row?.approval_status?.converted,
                    row?.approval_status?.color
                );
            },
        },
        {
            data: null,
            title: "Ghi chú phê duyệt",
            render: (data, type, row) => {
                return row?.approval_note || "";
            },
        },
        {
            data: null,
            title: "Ngày phê duyệt",
            render: (data, type, row) => {
                return row?.approval_date || "";
            },
        },
        {
            data: null,
            title: "Người phê duyệt",
            render: (data, type, row) => {
                return row?.approved_by?.name || "";
            },
        },
        {
            data: null,
            title: "Trạng thái phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return createBadge(
                    row?.adjust_approval_status?.converted,
                    row?.adjust_approval_status?.color
                );
            },
        },
        {
            data: null,
            title: "Ghi chú phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.adjust_approval_note || "";
            },
        },
        {
            data: null,
            title: "Ngày phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.adjust_approval_date || "";
            },
        },
        {
            data: null,
            title: "Người phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.adjust_approved_by?.name || "";
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
                return `
                    ${
                        row?.approval_status?.original == "pending"
                            ? createBtn(
                                  "primary",
                                  "Phê duyệt",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestApprove}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "approved",
                                  },
                                  "ti ti-check",
                                  "openModalApproveRequest(this)"
                              )?.outerHTML +
                              createBtn(
                                  "danger",
                                  "Từ chối",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestReject}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "rejected",
                                  },
                                  "ti ti-x",
                                  "openModalApproveRequest(this)"
                              )?.outerHTML
                            : ""
                    }
                    ${
                        row?.approval_status?.original == "approved" &&
                        ["none", "rejected"].includes(
                            row?.adjust_approval_status?.original
                        )
                            ? createBtn(
                                  "primary",
                                  "Yêu cầu điều chỉnh",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestAdjust}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                  },
                                  "ti ti-flag-check",
                                  "openModalAdjustRequest(this)"
                              )?.outerHTML
                            : ""
                    }
                    ${
                        row?.adjust_approval_status?.original == "pending"
                            ? createBtn(
                                  "primary",
                                  "Phê duyệt",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestAdjustApprove}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "approved",
                                  },
                                  "ti ti-check",
                                  "openModalAdjustApproveRequest(this)"
                              )?.outerHTML +
                              createBtn(
                                  "danger",
                                  "Từ chối",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestAdjustReject}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "rejected",
                                  },
                                  "ti ti-x",
                                  "openModalAdjustApproveRequest(this)"
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
    ];
};

const setFilterParams = () => {
    customDataTableFilterParams = {};

    if (createdBy.value)
        customDataTableFilterParams["created_by"] = createdBy.value;
    if (approvalStatus.value)
        customDataTableFilterParams["approval_status"] = approvalStatus.value;
    if (adjustApprovalStatus.value)
        customDataTableFilterParams["adjust_approval_status"] =
            adjustApprovalStatus.value;
    if (fromDateFilter.value)
        customDataTableFilterParams["from_date"] = fromDateFilter.value;
    if (toDateFilter.value)
        customDataTableFilterParams["to_date"] = toDateFilter.value;
};

[
    createdBy,
    approvalStatus,
    adjustApprovalStatus,
    fromDateFilter,
    toDateFilter,
].forEach((value, index) => {
    value.addEventListener("change", () => {
        setFilterParams();
        loadList();
    });
});
