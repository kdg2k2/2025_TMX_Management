// Lấy hoặc tạo span hiển thị format trong label
const getOrCreateFormattedSpan = (
    input,
    spanClass = "span-formatted-value text-muted text-info fw-normal ms-1"
) => {
    let label = null;

    // 1. Tìm label có thuộc tính for
    label = document.querySelector(`label[for='${input.id}']`);

    // 2. Nếu không tìm thấy, tìm label cùng cấp (sibling)
    if (!label) {
        label = input.parentElement?.querySelector("label");
    }

    // 3. Nếu vẫn không tìm thấy, tìm label ở cấp cha (parent)
    if (!label) {
        label = input.parentElement?.parentElement?.querySelector("label");
    }

    // 4. Tìm label ngay trước input (previousElementSibling)
    if (!label) {
        let prevElement = input.previousElementSibling;
        while (prevElement && prevElement.tagName !== "LABEL") {
            prevElement = prevElement.previousElementSibling;
        }
        label = prevElement;
    }

    // 5. Nếu input nằm trong label (label bọc input)
    if (!label) {
        label = input.closest("label");
    }

    if (!label) return null;

    let span = label.querySelector(".span-formatted-value");
    if (!span) {
        span = document.createElement("span");
        span.className = spanClass;
        label.appendChild(span);
    }
    return span;
};

// Cập nhật nội dung span
const updateFormattedSpan = (input, content = null) => {
    const span = getOrCreateFormattedSpan(input);
    if (!span) return;

    if (content !== null) {
        span.textContent = content;
    } else {
        const formatted = fmNumber(input.value || "");
        span.textContent = formatted ? `${formatted}` : "";
    }
};

// Xóa tất cả span formatted
const clearAllFormattedSpans = (parent = document) => {
    parent
        .querySelectorAll(".span-formatted-value")
        .forEach((el) => el.remove());
};
