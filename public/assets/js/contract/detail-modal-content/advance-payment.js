const contractAdvancePaymentModal = document.getElementById(
    "contract-advance-payment-modal"
);
const contractAdvancePaymentModalForm =
    contractAdvancePaymentModal.querySelector("form");
const advancePaymentAmount = document.getElementById("advance-payment-amount");
const advancePaymentInputValues = [advancePaymentAmount];
let formatAdvancePaymentTimeout;

const openAdvancePaymentModal = (
    id,
    method = "post",
    url = storeAdvancePaymentUrl,
    data = {}
) => {
    financeId = id;
    contractAdvancePaymentModalForm.setAttribute("action", url);

    const inMethod = contractAdvancePaymentModalForm?.querySelector(
        'input[name="_method"]'
    );
    if (inMethod) inMethod.value = method;

    if (method == "patch") {
        selectValueMapping = {};
        inputValueFormatter = {};
        autoMatchFieldAndFillPatchForm(
            contractAdvancePaymentModalForm,
            method,
            JSON.parse(data)
        );
    } else {
        contractAdvancePaymentModalForm.reset();
    }

    triggerChangeAdvancePaymentInputValues();

    showModal(contractAdvancePaymentModal);
};

// Cập nhật label hiển thị format
const updateAdvancePaymentSpanDisplayNumberFormart = (input) => {
    updateFormattedSpan(input, null);
};

// Xử lý thay đổi input
const advancePaymentHandleInputChange = (input) => {
    clearTimeout(formatAdvancePaymentTimeout);
    updateAdvancePaymentSpanDisplayNumberFormart(input);
    formatAdvancePaymentTimeout = setTimeout(() => {
        updateAdvancePaymentSpanDisplayNumberFormart(input);
    }, 1000);
};

const triggerChangeAdvancePaymentInputValues = () => {
    advancePaymentInputValues.forEach((input) => {
        advancePaymentHandleInputChange(input);
    });
};

contractAdvancePaymentModalForm.addEventListener("submit", async (e) => {
    appendFinanceIdInForm(contractAdvancePaymentModalForm);
    await handleSubmitForm(e, contractAdvancePaymentModalForm, () => {
        renderFinancesInfo();
        triggerChangeAdvancePaymentInputValues();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    applyIntegerValidation([advancePaymentAmount.getAttribute("id")]);

    advancePaymentInputValues.forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () =>
                advancePaymentHandleInputChange(input)
            );
        });
    });
});
