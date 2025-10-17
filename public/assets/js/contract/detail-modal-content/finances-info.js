const contractFinanceModal = document.getElementById("contract-finance-modal");
const contractFinanceModalForm = contractFinanceModal.querySelector("form");
const financesInfoDatatable = document.getElementById(
    "finances-info-datatable"
);
const financeRealizedValue = document.getElementById("finance-realized-value");
const financeAcceptanceValue = document.getElementById(
    "finance-acceptance-value"
);
const financeVatRate = document.getElementById("finance-vat-rate");
const financeVatAmount = document.getElementById("finance-vat-amount");
const financeInputValues = [
    financeRealizedValue,
    financeVatRate,
    financeAcceptanceValue,
];
let formatFinanceVATTimeout;

window.renderFinancesInfo = async () => {
    const response = await http.get(
        `${listFinanceUrl}?contract_id=${contractId}`
    );
    financesInfoDatatable.innerHTML = "";
    $("#finance-count").text(response?.data?.length || 0);

    if (response.data) {
        financesInfoDatatable.innerHTML =
            renderFinancesInfoThead(response.data) +
            renderFinancesInfoTbody(response.data);
    }
};

const renderFinancesInfoThead = (data) => {
    return `
        <thead class="table-primary">
            <tr>
                <th>Nội dung/Đơn vị</th>
                ${data
                    ?.map(
                        (value, index) =>
                            `
                                <th>
                                    <div class="d-flex justify-content-around align-items-center">
                                        <div>
                                            ${value?.contract_unit?.name}
                                        </div>
                                        <div>
                                            ${renderFinancesInfoActionButtons(
                                                value
                                            )}
                                        </div>
                                    </div>
                                </th>
                            `
                    )
                    .join("")}
            </tr>
        </thead>
    `;
};

const renderFinancesInfoTbody = (data) => {
    return `
        <tbody>
            <tr>
                <th>Vai trò</th>
                ${data
                    ?.map(
                        (value, index) => `<td>${value?.role?.converted}</td>`
                    )
                    .join("")}
            </tr>
            <tr>
                <th>Giá trị thực hiện(vnđ)</th>
                ${data
                    ?.map(
                        (value, index) =>
                            `<td>${fmNumber(value?.realized_value || 0)}</td>`
                    )
                    .join("")}
            </tr>
            <tr>
                <th>Giá trị nghiệm thu(vnđ)</th>
                ${data
                    ?.map(
                        (value, index) =>
                            `<td>${fmNumber(value?.acceptance_value || 0)}</td>`
                    )
                    .join("")}
            </tr>
            <tr>
                <th>Mức thuế(%)</th>
                ${data
                    ?.map((value, index) => `<td>${value?.vat_rate}</td>`)
                    .join("")}
            </tr>
            <tr>
                <th>VAT(vnđ)</th>
                ${data
                    ?.map(
                        (value, index) =>
                            `<td>${fmNumber(value?.vat_amount || 0)}</td>`
                    )
                    .join("")}
            </tr>
        </tbody>
    `;
};

const getFinancesInfoFilterParams = () => {
    const params = {
        paginate: 1,
        contract_id: contractId,
    };

    return params;
};

const renderFinancesInfoActionButtons = (row) => {
    return `
        ${
            createBtn(
                "warning",
                "Cập nhật",
                false,
                {},
                "ti ti-edit",
                `openFinanceModal("patch","${updateFinanceUrl}?id=${
                    row.id
                }", ${JSON.stringify(row)})`
            )?.outerHTML
        }
        ${createDeleteBtn(
            `${deleteFinanceUrl}?id=${row.id}`,
            "renderFinancesInfo"
        )}
    `;
};

const openFinanceModal = (
    method = "post",
    url = storeFinanceUrl,
    data = {}
) => {
    contractFinanceModalForm.setAttribute("action", url);

    const inMethod = contractFinanceModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {
            role: (item) => item.original,
        };
        inputValueFormatter = {};
        autoMatchFieldAndFillPatchForm(contractFinanceModalForm, method, data);
    } else {
        contractFinanceModalForm.reset();
        refreshSumoSelect($(contractFinanceModalForm).find("select"));
    }

    triggerChangeFinanceInputValues();

    showModal(contractFinanceModal);
};

// Tính VAT
const financeCalcVat = () => {
    updateVatAmount(financeAcceptanceValue, financeVatRate, financeVatAmount);
};

// Cập nhật label hiển thị format
const updateFinanceSpanDisplayNumberFormart = (input) => {
    if (input.id === financeVatRate.getAttribute("id")) return;
    updateFormattedSpan(input, null);
};

// Xử lý thay đổi input
const financeHandleInputChange = (input) => {
    clearTimeout(formatFinanceVATTimeout);
    financeCalcVat();
    updateFinanceSpanDisplayNumberFormart(input);
    formatFinanceVATTimeout = setTimeout(() => {
        financeCalcVat();
        updateFinanceSpanDisplayNumberFormart(input);
    }, 1000);
};

const triggerChangeFinanceInputValues = () => {
    financeInputValues.forEach((input) => {
        financeHandleInputChange(input);
    });
};

contractFinanceModalForm.addEventListener("submit", async (e) => {
    appendContractIdInForm(contractFinanceModalForm);
    await handleSubmitForm(e, contractFinanceModalForm, () => {
        renderFinancesInfo();
        triggerChangeFinanceInputValues();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    applyFloatValidation([financeVatRate.getAttribute("id")]);
    applyIntegerValidation([
        financeRealizedValue.getAttribute("id"),
        financeAcceptanceValue.getAttribute("id"),
        financeVatAmount.getAttribute("id"),
    ]);

    financeInputValues.forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () => financeHandleInputChange(input));
        });
    });
});
