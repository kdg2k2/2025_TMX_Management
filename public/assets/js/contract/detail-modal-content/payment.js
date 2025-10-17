const contractPaymentModal = document.getElementById("contract-payment-modal");
const contractPaymentModalForm = contractPaymentModal.querySelector("form");
const paymentAmount = document.getElementById("payment-amount");
const invoiceAmount = document.getElementById("invoice-amount");
const paymentInputValues = [paymentAmount, invoiceAmount];
let formatPaymentTimeout;

const openPaymentModal = (
    id,
    method = "post",
    url = storePaymentUrl,
    data = {}
) => {
    financeId = id;
    contractPaymentModalForm.setAttribute("action", url);

    const inMethod = contractPaymentModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {};
        inputValueFormatter = {};
        autoMatchFieldAndFillPatchForm(
            contractPaymentModalForm,
            method,
            JSON.parse(data)
        );
    } else {
        contractPaymentModalForm.reset();
    }

    triggerChangePaymentInputValues();

    showModal(contractPaymentModal);
};

// Cập nhật label hiển thị format
const updatePaymentSpanDisplayNumberFormart = (input) => {
    updateFormattedSpan(input, null);
};

// Xử lý thay đổi input
const paymentHandleInputChange = (input) => {
    clearTimeout(formatPaymentTimeout);
    updatePaymentSpanDisplayNumberFormart(input);
    formatPaymentTimeout = setTimeout(() => {
        updatePaymentSpanDisplayNumberFormart(input);
    }, 1000);
};

const triggerChangePaymentInputValues = () => {
    paymentInputValues.forEach((input) => {
        paymentHandleInputChange(input);
    });
};

contractPaymentModalForm.addEventListener("submit", async (e) => {
    appendFinanceIdInForm(contractPaymentModalForm);
    await handleSubmitForm(e, contractPaymentModalForm, () => {
        renderFinancesInfo();
        triggerChangePaymentInputValues();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation([
        paymentAmount.getAttribute("id"),
        invoiceAmount.getAttribute("id"),
    ]);

    paymentInputValues.forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () => paymentHandleInputChange(input));
        });
    });
});
