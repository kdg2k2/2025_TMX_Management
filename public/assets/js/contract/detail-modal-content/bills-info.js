const contractBillModal = document.getElementById("contract-bill-modal");
const contractBillModalForm = contractBillModal.querySelector("form");
const billTypeFilter = document.querySelector("bill-type-filter");
const billsInfoDatatable = document.getElementById("bills-info-datatable");

window.renderBillsInfo = () => {
    createDataTableServerSide(
        $(billsInfoDatatable),
        listBillUrl,
        renderBillsInfoColumns(),
        (item) => item,
        {
            paginate: 1,
        },
        (response) => {
            $("#bill-count").text(response.data.total);
        }
    );
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
            title: "Số tiền HĐ",
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
                      "Xem file",
                      false,
                      {},
                      "ti ti-eye-search",
                      `window.open('${row.path}', "_blank")`
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

    const inputContractId = contractBillModalForm?.querySelector(
        'input[name="contract_id"]'
    );
    if (inputContractId) inputContractId.value = contractId || "";

    const inMethod = contractBillModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        console.log(data);
        selectValueMapping = {
            bill_collector: (item) => item.id,
        };
        inputValueFormatter = {
            duration: (value) => formatDateToYmd(value),
        };
        autoMatchFieldAndFillPatchForm(contractBillModalForm, method, data);
    }

    showModal(contractBillModal);
};

contractBillModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, contractBillModalForm, () => {
        renderBillsInfo();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation(["bill-amount"]);
});
