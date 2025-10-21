const contractBillModal = document.getElementById("contract-bill-modal");
const contractBillModalForm = contractBillModal.querySelector("form");
const billTypeFilter = document.getElementById("bill-type-filter");
const billsInfoDatatable = document.getElementById("bills-info-datatable");
const billAmount = document.getElementById("bill-amount");
const billInputValues = [billAmount];
let formatBillAmoutTimeout;

window.renderBillsInfo = () => {
    createDataTableServerSide(
        $(billsInfoDatatable),
        listBillUrl,
        renderBillsInfoColumns(),
        (item) => item,
        getBillsInfoFilterParams(),
        (response) => {
            $("#bill-count").text(response.data.total);
        }
    );
};

const getBillsInfoFilterParams = () => {
    const params = {
        paginate: 1,
        contract_id: contractId,
    };

    if (billTypeFilter.value) params["bill_collector"] = billTypeFilter.value;

    return params;
};

const renderBillsInfoColumns = () => {
    return [
        {
            data: null,
            title: "Thời hạn",
            render: (data, type, row) => {
                return row?.duration;
            },
        },
        {
            data: null,
            title: "Số tiền HĐ(vnđ)",
            render: (data, type, row) => {
                return fmNumber(row?.amount || "");
            },
        },
        {
            data: null,
            title: "Nội dung dự toán",
            render: (data, type, row) => {
                return row?.content_in_the_estimate;
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
            title: "Người phụ trách lấy",
            render: (data, type, row) => {
                return row?.bill_collector?.name;
            },
        },
        {
            data: null,
            title: "Người tạo",
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
                return renderBillsInfoActionButtons(row);
            },
        },
    ];
};

const renderBillsInfoActionButtons = (row) => {
    return `
        ${
            row.path
                ? createBtn(
                      "info",
                      "Xem",
                      false,
                      {},
                      "ti ti-eye-search",
                      `viewFileHandler('${row.path}')`
                  )?.outerHTML
                : ""
        }
        ${
            createBtn(
                "warning",
                "Cập nhật",
                false,
                {},
                "ti ti-edit",
                `openBillModal("patch","${updateBillUrl}?id=${
                    row.id
                }", ${JSON.stringify(row)})`
            )?.outerHTML
        }
        ${createDeleteBtn(`${deleteBillUrl}?id=${row.id}`, "renderBillsInfo")}
    `;
};

const openBillModal = (method = "post", url = storeBillUrl, data = {}) => {
    contractBillModalForm.setAttribute("action", url);

    const inMethod = contractBillModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {
            bill_collector: (item) => item.id,
        };
        inputValueFormatter = {
            duration: (value) => formatDateToYmd(value),
        };
        autoMatchFieldAndFillPatchForm(contractBillModalForm, method, data);
    } else {
        contractBillModalForm.reset();
        refreshSumoSelect($(contractBillModalForm).find("select"));
    }

    triggerChangeBillInputValues();

    showModal(contractBillModal);
};

// Cập nhật label hiển thị format
const updateBillSpanDisplayNumberFormart = (input) => {
    updateFormattedSpan(input, null);
};

// Xử lý thay đổi input
const billHandleInputChange = (input) => {
    clearTimeout(formatBillAmoutTimeout);
    updateBillSpanDisplayNumberFormart(input);
    formatBillAmoutTimeout = setTimeout(() => {
        updateBillSpanDisplayNumberFormart(input);
    }, 1000);
};

const triggerChangeBillInputValues = () => {
    billInputValues.forEach((input) => {
        billHandleInputChange(input);
    });
};

contractBillModalForm.addEventListener("submit", async (e) => {
    appendContractIdInForm(contractBillModalForm);
    await handleSubmitForm(e, contractBillModalForm, () => {
        renderBillsInfo();
        triggerChangeBillInputValues();
    });
});

billTypeFilter.addEventListener("change", () => {
    renderBillsInfo();
});

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation([billAmount.getAttribute("id")]);

    billInputValues.forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () => billHandleInputChange(input));
        });
    });
});
