const createdBy = document.getElementById("created-by");
const approvalStatus = document.getElementById("approval-status");
const adjustApprovalStatus = document.getElementById("adjust-approval-status");
const fromDate = document.getElementById("from-date");
const toDate = document.getElementById("to-date");

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
                return row?.address || "";
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
            title: "Nội dung công tác",
            render: (data, type, row) => {
                return row?.content || "";
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
                    row?.return_approval_status?.converted,
                    row?.return_approval_status?.color
                );
            },
        },
        {
            data: null,
            title: "Ghi chú phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.return_approval_note || "";
            },
        },
        {
            data: null,
            title: "Ngày phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.return_approval_date || "";
            },
        },
        {
            data: null,
            title: "Người phê duyệt điều chỉnh",
            render: (data, type, row) => {
                return row?.return_approved_by?.name || "";
            },
        },
        {
            data: null,
            title: "Dữ liệu trước điều chỉnh",
            render: (data, type, row) => {
                return "";
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
                        row?.is_completed?.original == 0 &&
                        ["none", "rejected"].includes(
                            row?.return_approval_status?.original
                        )
                            ? createBtn(
                                  "primary",
                                  "Yêu cầu điều chỉnh",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestReturn}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                  },
                                  "ti ti-flag-check",
                                  "openModaAdjustRequest(this)"
                              )?.outerHTML
                            : ""
                    }
                    ${
                        row?.return_approval_status?.original == "pending"
                            ? createBtn(
                                  "primary",
                                  "Phê duyệt",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestReturnApprove}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "approved",
                                  },
                                  "ti ti-check",
                                  "openModaAdjustApproveRequest(this)"
                              )?.outerHTML +
                              createBtn(
                                  "danger",
                                  "Từ chối",
                                  false,
                                  {
                                      "data-href": `${apiLeaveRequestReturnReject}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "rejected",
                                  },
                                  "ti ti-x",
                                  "openModaAdjustApproveRequest(this)"
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
        customDataTableFilterParams["return_approval_status"] =
            adjustApprovalStatus.value;
    if (fromDate.value)
        customDataTableFilterParams["from_date"] = fromDate.value;
    if (toDate.value) customDataTableFilterParams["to_date"] = toDate.value;
};

[
    createdBy,
    approvalStatus,
    adjustApprovalStatus,
    fromDate,
    toDate,
].forEach((value, index) => {
    value.addEventListener("change", () => {
        setFilterParams();
        loadList();
    });
});
