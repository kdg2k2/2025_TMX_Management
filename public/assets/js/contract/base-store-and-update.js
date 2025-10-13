const contractValueInput = document.getElementById("contract_value");
const vatRateInput = document.getElementById("vat_rate");
const vatAmountInput = document.getElementById("vat_amount");
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
        // Bỏ chọn tất cả option nếu không phải hợp đồng nhiều năm
        Array.from(selectYears.options).forEach(
            (opt) => (opt.selected = false)
        );
        initSumoSelect(select);
    }
};

// Lấy hoặc tạo span hiển thị format trong label
const getLabelSpan = (input) => {
    const label = document.querySelector(`label[for='${input.id}']`);
    if (!label) return null;
    let span = label.querySelector(".formatted-value");
    if (!span) {
        span = document.createElement("span");
        span.className = "formatted-value text-muted text-info fw-normal ms-1";
        label.appendChild(span);
    }
    return span;
};

const clearLabelSpans = () => {
    document.querySelectorAll(".formatted-value").forEach((el) => el.remove());
};

// Tính tiền thuế
const calcVat = () => {
    const contractValue = contractValueInput.value;
    const rate = vatRateInput.value;
    if (contractValue > 0 && rate > 0) {
        const vat = Math.round(
            (contractValue / (1 + rate / 100)) * (rate / 100)
        );
        // input giữ raw value
        vatAmountInput.value = vat.toFixed(0);
        // label hiển thị formatted
        const span = getLabelSpan(vatAmountInput);
        if (span)
            span.textContent = `${fmNumber(
                contractValue
            )}/(1 + ${rate}%) * ${rate}% = ${fmNumber(vat)} (Đã làm tròn)`;
    } else {
        vatAmountInput.value = "";
        const span = getLabelSpan(vatAmountInput);
        if (span) span.textContent = "";
    }
};

// Cập nhật label hiển thị format
const updateFormattedLabel = (input) => {
    if (input.id === "vat_rate") return; // không format phần trăm
    const span = getLabelSpan(input);
    if (span) {
        const formatted = fmNumber(input.value);
        span.textContent = formatted ? `${formatted}` : "";
    }
};

// Lắng nghe thay đổi giá trị
const handleInputChange = (input) => {
    clearTimeout(formatTimeout);
    calcVat();
    updateFormattedLabel(input);
    formatTimeout = setTimeout(() => {
        calcVat();
        updateFormattedLabel(input);
    }, 1000);
};

const refreshSubmitFrom = () => {
    refreshSumoSelect();
    toggleManyYear();
    clearLabelSpans();
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

    // Gắn event cho 2 input chính
    [contractValueInput, vatRateInput].forEach((input) => {
        ["input", "paste", "change", "blur"].forEach((evt) => {
            input.addEventListener(evt, () => handleInputChange(input));
        });
    });
});
