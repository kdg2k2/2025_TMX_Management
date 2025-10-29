const fromDate = document.querySelector('[name="from_date"]');
const toDate = document.querySelector('[name="to_date"]');
const totalLeaveDays = document.querySelector('[name="total_leave_days"]');

const calculateTotalLeaveDays = async () => {
    const fromDateVal = fromDate.value;
    const toDateVal = toDate.value;
    if (fromDateVal && toDateVal) {
        const res = await http.post("/api/leave-request/get-total-leave-days", {
            from_date: fromDateVal,
            to_date: toDateVal,
        });

        if (res.data) totalLeaveDays.value = res.data;
    }
};

[fromDate, toDate].forEach((element) => {
    element.addEventListener("change", calculateTotalLeaveDays);
});
