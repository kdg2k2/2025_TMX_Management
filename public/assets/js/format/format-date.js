const formatDateToYmd = (dateStr) => {
    if (!dateStr) return "";
    // Xử lý format d/m/Y hoặc d-m-Y
    const parts = dateStr.split(/[\/\-]/);
    if (parts.length === 3) {
        const [day, month, year] = parts;
        return `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
    }
    return dateStr; // Trả về nguyên gốc nếu không match
};

const formatDateTime = (dateString) => {
    if (!dateString || typeof dateString !== "string") {
        return dateString;
    }

    try {
        // Tạo Date object từ string
        const date = new Date(dateString);

        // Kiểm tra date có hợp lệ không
        if (isNaN(date.getTime())) {
            return "";
        }

        // Helper function để thêm số 0 phía trước
        const pad = (num) => num.toString().padStart(2, "0");

        // Lấy các thành phần ngày tháng năm
        const day = pad(date.getDate());
        const month = pad(date.getMonth() + 1);
        const year = date.getFullYear();
        const hours = pad(date.getHours());
        const minutes = pad(date.getMinutes());
        const seconds = pad(date.getSeconds());

        // Kiểm tra xem string có chứa thông tin thời gian không
        const hasTime = dateString.includes("T") || dateString.includes(" ");

        // Kiểm tra xem có phải chỉ là time không (không có ngày tháng)
        const isOnlyTime = /^\d{2}:\d{2}(:\d{2})?$/.test(dateString.trim());

        // Kiểm tra xem có phải chỉ là date không
        const isOnlyDate = /^\d{4}-\d{2}-\d{2}$/.test(dateString.trim());

        if (isOnlyTime) {
            // Chỉ trả về time
            return `${hours}:${minutes}:${seconds}`;
        } else if (isOnlyDate) {
            // Chỉ trả về date
            return `${day}/${month}/${year}`;
        } else if (hasTime) {
            // Trả về datetime đầy đủ
            return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        } else {
            // Mặc định trả về date
            return `${day}/${month}/${year}`;
        }
    } catch (error) {
        console.error("Error formatting date:", error);
        return "";
    }
};

// Phiên bản nâng cao hơn với nhiều tùy chọn
const formatDateTimeAdvanced = (dateString, options = {}) => {
    const {
        dateFormat = "dd/mm/yyyy",
        timeFormat = "HH:ii:ss",
        separator = " ",
        forceFormat = null, // 'date', 'time', 'datetime'
    } = options;

    if (!dateString || typeof dateString !== "string") {
        return "";
    }

    try {
        const date = new Date(dateString);

        if (isNaN(date.getTime())) {
            return "";
        }

        const pad = (num) => num.toString().padStart(2, "0");

        const day = pad(date.getDate());
        const month = pad(date.getMonth() + 1);
        const year = date.getFullYear();
        const hours = pad(date.getHours());
        const minutes = pad(date.getMinutes());
        const seconds = pad(date.getSeconds());

        // Format date theo pattern
        const formattedDate = dateFormat
            .replace("dd", day)
            .replace("mm", month)
            .replace("yyyy", year)
            .replace("yy", year.toString().slice(-2));

        // Format time theo pattern
        const formattedTime = timeFormat
            .replace("HH", hours)
            .replace("ii", minutes)
            .replace("ss", seconds);

        // Xác định loại format
        let formatType = forceFormat;
        if (!formatType) {
            const hasTime =
                dateString.includes("T") || dateString.includes(" ");
            const isOnlyTime = /^\d{2}:\d{2}(:\d{2})?$/.test(dateString.trim());
            const isOnlyDate = /^\d{4}-\d{2}-\d{2}$/.test(dateString.trim());

            if (isOnlyTime) {
                formatType = "time";
            } else if (isOnlyDate) {
                formatType = "date";
            } else if (hasTime) {
                formatType = "datetime";
            } else {
                formatType = "date";
            }
        }

        switch (formatType) {
            case "time":
                return formattedTime;
            case "date":
                return formattedDate;
            case "datetime":
                return `${formattedDate}${separator}${formattedTime}`;
            default:
                return formattedDate;
        }
    } catch (error) {
        console.error("Error formatting date:", error);
        return "";
    }
};

const formatDateForInput = (dateStr) => {
    if (!dateStr) return "";
    if (dateStr.includes("-")) return dateStr; // Đã đúng format

    const [day, month, year] = dateStr.split("/");
    if (day && month && year) {
        return `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
    }
    return "";
};
