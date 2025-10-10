// Base class cho việc validate số
class NumericValidator {
    constructor(inputIds, options = {}) {
        this.inputIds = Array.isArray(inputIds) ? inputIds : [inputIds];
        this.options = {
            decimalPlaces: options.decimalPlaces || 0,
            allowNegative: options.allowNegative || false,
            ...options,
        };
    }

    // Hàm validate cơ bản - sẽ được override
    validateChar(char, currentValue) {
        return /^\d$/.test(char);
    }

    // Hàm validate giá trị paste - có thể override nếu cần
    validatePastedData(pastedData) {
        return /^\d*$/.test(pastedData);
    }

    // Hàm format giá trị - sẽ được override nếu cần
    formatValue(value) {
        return value;
    }

    // Hàm setup cơ bản cho input
    setupInput(element) {
        element.setAttribute("type", "text");
        element.setAttribute("inputmode", "numeric");
    }

    // Hàm xử lý sự kiện keypress
    handleKeyPress(event) {
        const currentValue = event.target.value;
        const char = String.fromCharCode(event.keyCode);

        if (!this.validateChar(char, currentValue)) {
            event.preventDefault();
        }
    }

    // Hàm xử lý sự kiện paste
    handlePaste(event) {
        event.preventDefault();
        const pastedData = (
            event.clipboardData || window.clipboardData
        ).getData("text");

        if (this.validatePastedData(pastedData)) {
            const element = event.target;
            const currentValue = element.value;
            const cursorPos = element.selectionStart;

            const newValue =
                currentValue.slice(0, cursorPos) +
                pastedData +
                currentValue.slice(element.selectionEnd);

            if (this.validatePastedData(newValue)) {
                element.value = newValue;
            }
        }
    }

    // Hàm xử lý sự kiện blur
    handleBlur(event) {
        const value = event.target.value;
        if (value) {
            event.target.value = this.formatValue(value);
        }
    }

    // Hàm xử lý sự kiện change
    handleChange(event) {
        const value = event.target.value;
        if (value && isNaN(value)) {
            event.target.value = "";
        }
    }

    // Hàm áp dụng validation
    apply() {
        this.inputIds.forEach((id) => {
            const element = document.getElementById(id);
            if (!element) {
                console.warn(`Không tìm thấy element với id: ${id}`);
                return;
            }

            this.setupInput(element);

            element.addEventListener(
                "keypress",
                this.handleKeyPress.bind(this)
            );
            element.addEventListener("paste", this.handlePaste.bind(this));
            element.addEventListener("blur", this.handleBlur.bind(this));
            element.addEventListener("change", this.handleChange.bind(this));
        });
    }
}

// Class cho số nguyên
class IntegerValidator extends NumericValidator {
    validateChar(char, currentValue) {
        if (this.options.allowNegative && char === "-" && !currentValue) {
            return true;
        }
        return /^\d$/.test(char);
    }

    validatePastedData(pastedData) {
        if (this.options.allowNegative) {
            return /^-?\d*$/.test(pastedData);
        }
        return /^\d*$/.test(pastedData);
    }

    formatValue(value) {
        return parseInt(value, 10);
    }
}

// Class cho số thập phân
class FloatValidator extends NumericValidator {
    validateChar(char, currentValue) {
        if (this.options.allowNegative && char === "-" && !currentValue) {
            return true;
        }
        if (char === "." && currentValue && !currentValue.includes(".")) {
            return true;
        }
        return /^\d$/.test(char);
    }

    validatePastedData(pastedData) {
        if (this.options.allowNegative) {
            return /^-?\d*\.?\d*$/.test(pastedData);
        }
        return /^\d*\.?\d*$/.test(pastedData);
    }

    formatValue(value) {
        return Number(value).toFixed(this.options.decimalPlaces);
    }

    handleKeyPress(event) {
        super.handleKeyPress(event);

        if (this.options.decimalPlaces) {
            const currentValue = event.target.value;
            if (currentValue.includes(".")) {
                const [, decimal] = currentValue.split(".");
                if (decimal && decimal.length >= this.options.decimalPlaces) {
                    event.preventDefault();
                }
            }
        }
    }
}

// Wrapper functions để duy trì API cũ
const applyIntegerValidation = (
    inputIds,
    options = { allowNegative: false }
) => {
    const validator = new IntegerValidator(inputIds, options);
    validator.apply();
};

const applyFloatValidation = (
    inputIds,
    decimalPlaces = null,
    options = { allowNegative: false }
) => {
    const validator = new FloatValidator(inputIds, {
        ...options,
        decimalPlaces,
    });
    validator.apply();
};
