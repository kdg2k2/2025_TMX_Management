const leaveType = document.getElementById("leave-type");
const fromDate = document.getElementById("from-date");
const toDate = document.getElementById("to-date");
const toDateWrapper = document.getElementById("to-date-wrapper");
const totalLeaveDays = document.getElementById("total-leave-days");

// Toggle hiển thị to_date dựa vào type
const toggleToDateVisibility = () => {
    const type = leaveType.value;

    if (type === "many_days") {
        // Nhiều ngày: hiển thị cả 2 input
        toDateWrapper.hidden = false;
        toDate.required = true;
    } else {
        // 1 ngày, sáng, chiều: ẩn to_date và tự động gán = from_date
        toDateWrapper.hidden = true;
        toDate.required = false;
        toDate.value = fromDate.value;
    }
};

// Tính tổng số ngày nghỉ
const calculateTotalLeaveDays = async () => {
    const type = leaveType.value;
    const fromDateVal = fromDate.value;
    const toDateVal = toDate.value;

    if (!fromDateVal || !toDateVal) {
        totalLeaveDays.value = "";
        return;
    }

    try {
        const res = await http.post("/api/leave-request/get-total-leave-days", {
            from_date: fromDateVal,
            to_date: toDateVal,
            type: type,
        });

        if (res.data) {
            totalLeaveDays.value = res.data;
        }
    } catch (error) {
        console.error("Error calculating leave days:", error);
        totalLeaveDays.value = "";
    }
};

// Event: Khi đổi type
leaveType.addEventListener("change", () => {
    toggleToDateVisibility();
    calculateTotalLeaveDays();
});

// Event: Khi đổi from_date
fromDate.addEventListener("change", () => {
    const type = leaveType.value;

    // Nếu không phải nhiều ngày, tự động gán to_date = from_date
    if (type !== "many_days") {
        toDate.value = fromDate.value;
    }

    calculateTotalLeaveDays();
});

// Event: Khi đổi to_date
toDate.addEventListener("change", calculateTotalLeaveDays);

// Init khi load trang
document.addEventListener("DOMContentLoaded", () => {
    toggleToDateVisibility();
});
