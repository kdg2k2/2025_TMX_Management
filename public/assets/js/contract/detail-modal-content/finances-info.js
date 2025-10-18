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
    // số lần tạm ứng lớn nhất
    const maxAdvanceTimes = Math.max(
        ...data.map((d) => d.advance_payment?.length || 0)
    );
    // số lần thanh toán lớn nhất
    const maxPaymentTimes = Math.max(
        ...data.map((d) => d.payment?.length || 0)
    );

    let tbody = `
        <tbody>
            <tr>
                <th>Vai trò</th>
                ${data
                    .map((v) => `<td>${v?.role?.converted || ""}</td>`)
                    .join("")}
            </tr>
            <tr>
                <th>Giá trị thực hiện(vnđ)</th>
                ${data
                    .map((v) => `<td>${fmNumber(v?.realized_value || 0)}</td>`)
                    .join("")}
            </tr>
            <tr>
                <th>Giá trị nghiệm thu(vnđ)</th>
                ${data
                    .map(
                        (v) => `<td>${fmNumber(v?.acceptance_value || 0)}</td>`
                    )
                    .join("")}
            </tr>
            <tr>
                <th>Mức thuế(%)</th>
                ${data.map((v) => `<td>${v?.vat_rate || 0}</td>`).join("")}
            </tr>
            <tr>
                <th>VAT(vnđ)</th>
                ${data
                    .map((v) => `<td>${fmNumber(v?.vat_amount || 0)}</td>`)
                    .join("")}
            </tr>
    `;

    // tạm ứng lần N
    for (let i = 0; i < maxAdvanceTimes; i++) {
        tbody += `
            <tr>
                <th>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Tạm ứng lần ${i + 1}</div>
                    </div>
                </th>
                ${data
                    .map((v) => {
                        const advance = v.advance_payment?.[i];
                        return `
                            <td>
                                ${
                                    advance
                                        ? `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <ul class="m-0 list-unstyled">
                                                    <li><span class="fw-bold">Số tiền: </span>${fmNumber(
                                                        advance.amount
                                                    )}vnđ</li>
                                                    <li><span class="fw-bold">Ngày: </span>
                                                        ${formatDateTime(
                                                            advance.date
                                                        )}
                                                    </li>
                                                </ul>
                                                <div>
                                                    ${
                                                        createBtn(
                                                            "outline-secondary",
                                                            "Cập nhật tạm ứng",
                                                            false,
                                                            {},
                                                            "ti ti-edit",
                                                            `openAdvancePaymentModal(${
                                                                advance.contract_finance_id
                                                            }, 'patch', '${updateAdvancePaymentUrl}?id=${
                                                                advance.id
                                                            }', '${JSON.stringify(
                                                                advance
                                                            )}')`
                                                        )?.outerHTML
                                                    }
                                                    ${createDeleteBtn(
                                                        `${deleteAdvancePaymentUrl}?id=${advance.id}`,
                                                        "renderFinancesInfo"
                                                    )}
                                                </div>
                                            </div>
                                        `
                                        : "-"
                                }
                            </td>
                        `;
                    })
                    .join("")}
            </tr>
        `;
    }

    // thanh toán lần N
    for (let i = 0; i < maxPaymentTimes; i++) {
        tbody += `
            <tr>
                <th>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>Thanh toán lần ${i + 1}</div>
                    </div>
                </th>
                ${data
                    .map((v) => {
                        const payment = v.payment?.[i];
                        return `
                            <td>
                                ${
                                    payment
                                        ? `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <ul class="m-0 list-unstyled">
                                                    <li><span class="fw-bold">Số tiền thanh toán: </span>${fmNumber(
                                                        payment.payment_amount
                                                    )}vnđ</li>
                                                    <li><span class="fw-bold">Ngày thanh toán: </span>
                                                        ${formatDateTime(
                                                            payment.payment_date
                                                        )}
                                                    </li>
                                                    <li><span class="fw-bold">Số tiền hóa đơn: </span>${fmNumber(
                                                        payment.invoice_amount
                                                    )}vnđ</li>
                                                    <li><span class="fw-bold">Ngày hóa đơn: </span>
                                                        ${formatDateTime(
                                                            payment.invoice_date
                                                        )}
                                                    </li>
                                                    <li><span class="fw-bold">Số hóa đơn: </span>${
                                                        payment.invoice_number
                                                    }</li>
                                                </ul>
                                                <div>
                                                    ${
                                                        createBtn(
                                                            "outline-primary",
                                                            "Cập nhật thanh toán",
                                                            false,
                                                            {},
                                                            "ti ti-edit",
                                                            `openPaymentModal(${
                                                                payment.contract_finance_id
                                                            }, 'patch', '${updatePaymentUrl}?id=${
                                                                payment.id
                                                            }', '${JSON.stringify(
                                                                payment
                                                            )}')`
                                                        )?.outerHTML
                                                    }
                                                    ${createDeleteBtn(
                                                        `${deletePaymentUrl}?id=${payment.id}`,
                                                        "renderFinancesInfo"
                                                    )}
                                                </div>
                                            </div>
                                        `
                                        : "-"
                                }
                            </td>
                        `;
                    })
                    .join("")}
            </tr>
        `;
    }

    tbody += `
        <tr class="text-success">
            <th>Tổng tiền tạm ứng + thanh toán(vnđ)</th>
            ${data
                .map((v) => {
                    const total =
                        (v.advance_payment?.reduce(
                            (sum, a) => sum + (a.amount || 0),
                            0
                        ) || 0) +
                        (v.payment?.reduce(
                            (sum, a) => sum + (a.payment_amount || 0),
                            0
                        ) || 0);
                    return `<th>${fmNumber(total)}</th>`;
                })
                .join("")}
        </tr>
    `;

    tbody += `
        <tr class="fw-bold text-danger">
            <th>Còn nợ(vnđ)</th>
            ${data
                .map((v) => {
                    const totalAdvance =
                        v.advance_payment?.reduce(
                            (sum, a) => sum + (a.amount || 0),
                            0
                        ) || 0;
                    const totalPayment =
                        v.payment?.reduce(
                            (sum, a) => sum + (a.payment_amount || 0),
                            0
                        ) || 0;
                    const totalPaid = totalAdvance + totalPayment;
                    const remaining = (v.acceptance_value || 0) - totalPaid;

                    return `<th>${fmNumber(remaining)}</th>`;
                })
                .join("")}
        </tr>
    `;

    tbody += "</tbody>";

    return tbody;
};

const renderFinancesInfoActionButtons = (row) => {
    return `
        ${
            createBtn(
                "primary",
                "Thêm thanh toán",
                false,
                {},
                "ti ti-tax",
                `openPaymentModal(${row.id})`
            )?.outerHTML
        }
        ${
            createBtn(
                "secondary",
                "Thêm tạm ứng",
                false,
                {},
                "ti ti-credit-card-pay",
                `openAdvancePaymentModal(${row.id})`
            )?.outerHTML
        }
        ${
            createBtn(
                "warning",
                "Cập nhật đơn vị",
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
