// Tính thuế VAT
const calculateVat = (contractValue, vatRate) => {
    if (contractValue <= 0 || vatRate <= 0) {
        return {
            value: 0,
            formula: "",
            isValid: false,
        };
    }

    const vatAmount = Math.round(
        (contractValue / (1 + vatRate / 100)) * (vatRate / 100)
    );

    const formula = `${fmNumber(
        contractValue
    )}/(1 + ${vatRate}%) * ${vatRate}% = ${fmNumber(vatAmount)} (Đã làm tròn)`;

    return {
        value: vatAmount,
        formula: formula,
        isValid: true,
    };
};

// Cập nhật input VAT amount và hiển thị công thức
const updateVatAmount = (valueInput, rateInput, vatAmountInput) => {
    const contractValue = valueInput.value;
    const vatRate = rateInput.value;
    const result = calculateVat(contractValue, vatRate);

    if (result.isValid) {
        vatAmountInput.value = result.value.toFixed(0);
        updateFormattedSpan(vatAmountInput, result.formula);
    } else {
        vatAmountInput.value = "";
        updateFormattedSpan(vatAmountInput, "");
    }
};
