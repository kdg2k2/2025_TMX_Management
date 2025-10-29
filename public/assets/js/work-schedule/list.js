const createdBy = document.getElementById("created-by");
const typeProgram = document.getElementById("type-program");
const contractsId = document.getElementById("contracts-id");
const approvalStatus = document.getElementById("approval-status");
const returnApprovalStatus = document.getElementById("return-approval-status");
const isCompleted = document.getElementById("is-completed");
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
            title: "Địa chỉ",
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
                        ${row?.total_trip_days} ngày
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
            title: "Chương trình",
            render: (data, type, row) => {
                return `
                    <div class="text-center">
                        ${row?.type_program?.converted}
                        <br>
                        <i>
                            ${
                                row?.type_program?.original == "contract"
                                    ? row?.contract?.name
                                    : row.other_program
                            }
                        </i>
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Đầu mối",
            render: (data, type, row) => {
                return row?.clue || "";
            },
        },
        {
            data: null,
            title: "Thành phần",
            render: (data, type, row) => {
                return row?.participants || "";
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.note || "";
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
            title: "Trạng thái phê duyệt kết thúc công tác",
            render: (data, type, row) => {
                return createBadge(
                    row?.return_approval_status?.converted,
                    row?.return_approval_status?.color
                );
            },
        },
        {
            data: null,
            title: "Thời gian về",
            render: (data, type, row) => {
                return row?.return_datetime || "";
            },
        },
        {
            data: null,
            title: "Ghi chú phê duyệt kết thúc công tác",
            render: (data, type, row) => {
                return row?.return_approval_note || "";
            },
        },
        {
            data: null,
            title: "Ngày phê duyệt kết thúc công tác",
            render: (data, type, row) => {
                return row?.return_approval_date || "";
            },
        },
        {
            data: null,
            title: "Người phê duyệt kết thúc công tác",
            render: (data, type, row) => {
                return row?.return_approved_by?.name || "";
            },
        },
        {
            data: null,
            title: "Đã kết thúc công tác?",
            render: (data, type, row) => {
                return createBadge(
                    row?.is_completed?.converted,
                    row?.is_completed?.color
                );
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
                                      "data-href": `${apiWorkScheduleApprove}?id=${row?.id}`,
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
                                      "data-href": `${apiWorkScheduleReject}?id=${row?.id}`,
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
                                  "Yêu cầu kết thúc công tác",
                                  false,
                                  {
                                      "data-href": `${apiWorkScheduleReturn}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                  },
                                  "ti ti-flag-check",
                                  "openModalReturnRequest(this)"
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
                                      "data-href": `${apiWorkScheduleReturnApprove}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "approved",
                                  },
                                  "ti ti-check",
                                  "openModalReturnApproveRequest(this)"
                              )?.outerHTML +
                              createBtn(
                                  "danger",
                                  "Từ chối",
                                  false,
                                  {
                                      "data-href": `${apiWorkScheduleReturnReject}?id=${row?.id}`,
                                      "data-onsuccess": "loadList",
                                      "data-approve-status": "rejected",
                                  },
                                  "ti ti-x",
                                  "openModalReturnApproveRequest(this)"
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
    if (typeProgram.value)
        customDataTableFilterParams["type_program"] = typeProgram.value;
    if (contractsId.value)
        customDataTableFilterParams["contracts_id"] = contractsId.value;
    if (approvalStatus.value)
        customDataTableFilterParams["approval_status"] = approvalStatus.value;
    if (returnApprovalStatus.value)
        customDataTableFilterParams["return_approval_status"] =
            returnApprovalStatus.value;
    if (isCompleted.value)
        customDataTableFilterParams["is_completed"] = isCompleted.value;
    if (fromDate.value)
        customDataTableFilterParams["from_date"] = fromDate.value;
    if (toDate.value) customDataTableFilterParams["to_date"] = toDate.value;
};

[
    createdBy,
    typeProgram,
    contractsId,
    approvalStatus,
    returnApprovalStatus,
    isCompleted,
    fromDate,
    toDate,
].forEach((value, index) => {
    value.addEventListener("change", () => {
        setFilterParams();
        loadList();
    });
});
