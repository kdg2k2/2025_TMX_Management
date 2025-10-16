const contractValueInput = document.getElementById("contract_value");
const vatRateInput = document.getElementById("vat_rate");
const vatAmountInput = document.getElementById("vat_amount");
const acceptanceValueInput = document.getElementById("acceptance_value");
const liquidationValueInput = document.getElementById("liquidation_value");
let formatTimeout;
const selectManyYear = document.getElementById("is_contract_many_year");
const colManyYear = document.querySelector("#many_years").closest(".col-md-4");
const selectYears = document.getElementById("many_years");

const toggleManyYear = () => {
    const isMany = selectManyYear.value === "1";
    colManyYear.hidden = !isMany;
    if (!isMany) {
        const select = $(selectYears);
        destroySumoSelect(select);
        Array.from(selectYears.options).forEach(
            (opt) => (opt.selected = false)
        );
        initSumoSelect(select);
    }
};

// Tính VAT
const calcVat = () => {
    updateVatAmount(contractValueInput, vatRateInput, vatAmountInput);
};

// Cập nhật label hiển thị format
const updateSpanDisplayNumberFormart = (input) => {
    if (input.id === "vat_rate") return;
    updateFormattedSpan(input, null);
};

// Xử lý thay đổi input
const handleInputChange = (input) => {
    clearTimeout(formatTimeout);
    calcVat();
    updateSpanDisplayNumberFormart(input);
    formatTimeout = setTimeout(() => {
        calcVat();
        updateSpanDisplayNumberFormart(input);
    }, 1000);
};

// Refresh form
const refreshSubmitFrom = () => {
    toggleManyYear();
    clearAllFormattedSpans();
    calcVat();
};

selectManyYear.addEventListener("change", toggleManyYear);

document.addEventListener("DOMContentLoaded", () => {
    toggleManyYear();

    applyIntegerValidation([
        "contract_value",
        "vat_rate",
        "acceptance_value",
        "liquidation_value",
    ]);

    const inputValues = [
        contractValueInput,
        vatRateInput,
        acceptanceValueInput,
        liquidationValueInput,
    ];

    inputValues.forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () => handleInputChange(input));
        });
    });

    setTimeout(() => {
        inputValues.forEach((input) => {
            handleInputChange(input);
        });
    }, 500);
});
