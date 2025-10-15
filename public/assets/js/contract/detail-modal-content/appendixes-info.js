const contractAppendixModal = document.getElementById(
    "contract-appendix-modal"
);
const contractAppendixModalForm = contractAppendixModal.querySelector("form");
const appendixesInfoDatatable = document.getElementById(
    "appendixes-info-datatable"
);

window.renderAppendixesInfo = () => {
    createDataTableServerSide(
        $(appendixesInfoDatatable),
        listAppendixUrl,
        renderAppendixesInfoColumns(),
        (item) => item,
        getAppendixesInfoFilterParams(),
        (response) => {
            $("#appendix-count").text(response.data.total);
        }
    );
};

const getAppendixesInfoFilterParams = () => {
    const params = {
        paginate: 1,
        contract_id: contractId,
    };

    return params;
};

const renderAppendixesInfoColumns = () => {
    return [
        {
            data: null,
            title: "Nội dung",
            render: (data, type, row) => {
                return row?.content;
            },
        },
        {
            data: null,
            title: "Công văn gia hạn",
            render: (data, type, row) => {
                return `
                    ${
                        row.renewal_letter
                            ? createBtn(
                                  "info",
                                  "Xem",
                                  false,
                                  {},
                                  "ti ti-eye-search",
                                  `window.open('${row.renewal_letter}', "_blank")`
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
        {
            data: null,
            title: "Công văn đồng ý gia hạn",
            render: (data, type, row) => {
                return `
                    ${
                        row.renewal_approval_letter
                            ? createBtn(
                                  "info",
                                  "Xem",
                                  false,
                                  {},
                                  "ti ti-eye-search",
                                  `window.open('${row.renewal_approval_letter}', "_blank")`
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
        {
            data: null,
            title: "Phụ lục gia hạn",
            render: (data, type, row) => {
                return `
                    ${
                        row.renewal_appendix
                            ? createBtn(
                                  "info",
                                  "Xem",
                                  false,
                                  {},
                                  "ti ti-eye-search",
                                  `window.open('${row.renewal_appendix}', "_blank")`
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
        {
            data: null,
            title: "Hồ sơ khác",
            render: (data, type, row) => {
                return `
                    ${
                        row.other_documents
                            ? createBtn(
                                  "info",
                                  "Xem",
                                  false,
                                  {},
                                  "ti ti-eye-search",
                                  `window.open('${row.other_documents}', "_blank")`
                              )?.outerHTML
                            : ""
                    }
                `;
            },
        },
        {
            data: null,
            title: "Ngày gia hạn",
            render: (data, type, row) => {
                return row?.renewal_date;
            },
        },
        {
            data: null,
            title: "Ngày kết thúc gia hạn",
            render: (data, type, row) => {
                return row?.renewal_end_date;
            },
        },
        {
            data: null,
            title: "Giá trị điều chỉnh",
            render: (data, type, row) => {
                return fmNumber(row?.adjusted_value || "");
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.note;
            },
        },
        {
            data: null,
            title: "Người đăng tải",
            render: (data, type, row) => {
                return row?.created_by?.name;
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
                return renderAppendixesInfoActionButtons(row);
            },
        },
    ];
};

const renderAppendixesInfoActionButtons = (row) => {
    return `
        ${
            createBtn(
                "warning",
                "Cập nhật",
                false,
                {},
                "ti ti-edit",
                `openAppendixModal("patch","${updateAppendixUrl}?id=${
                    row.id
                }", ${JSON.stringify(row)})`
            )?.outerHTML
        }
        ${createDeleteBtn(
            `${deleteAppendixUrl}?id=${row.id}`,
            "renderAppendixesInfo"
        )}
    `;
};

const openAppendixModal = (
    method = "post",
    url = storeAppendixUrl,
    data = {}
) => {
    contractAppendixModalForm.setAttribute("action", url);

    const inputContractId = contractAppendixModalForm?.querySelector(
        'input[name="contract_id"]'
    );
    if (inputContractId) inputContractId.value = contractId || "";

    const inMethod = contractAppendixModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {};
        inputValueFormatter = {
            renewal_date: (value) => formatDateToYmd(value),
            renewal_end_date: (value) => formatDateToYmd(value),
        };
        autoMatchFieldAndFillPatchForm(contractAppendixModalForm, method, data);
    }

    showModal(contractAppendixModal);
};

contractAppendixModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, contractAppendixModalForm, () => {
        renderAppendixesInfo();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation(["appendix-adjusted-value"]);
});
