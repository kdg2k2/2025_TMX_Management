// Format số
const fmNumber = (value) => {
    // Parse nếu là string
    let numValue = value;
    if (typeof value === "string") {
        // Loại bỏ khoảng trắng và parse
        numValue = parseFloat(value.trim());
        // Nếu parse không được thì return chuỗi gốc hoặc rỗng
        if (isNaN(numValue)) {
            return value.trim() || "";
        }
    }

    if (typeof numValue === "number" && !isNaN(numValue)) {
        if (Number.isInteger(numValue)) {
            return numValue.toLocaleString("vi-VN", {
                minimumFractionDigits: 0,
            });
        } else {
            // Đếm số chữ số thập phân thực tế
            const decimalPlaces = (numValue.toString().split(".")[1] || "")
                .length;
            return numValue.toLocaleString("vi-VN", {
                minimumFractionDigits: 0,
                maximumFractionDigits: Math.max(decimalPlaces, 2),
            });
        }
    }

    return "";
};
