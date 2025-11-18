customDataTableFilterParams = {
    year: $year,
    month: $month,
};
const table = $("#datatable");
const modalUpload = document.getElementById("modal-upload");
const modalUploadForm = modalUpload.querySelector("form");

const renderColumns = () => {
    return [
        {
            data: null,
            title: "Họ tên",
            render: (data, type, row) => {
                return row?.user?.name || "";
            },
        },
        {
            data: null,
            title: "Tổng công thêm",
            render: (data, type, row) => {
                return row?.overtime_total_count || "";
            },
        },
        {
            data: null,
            title: "Tổng ngày nghỉ không phép",
            render: (data, type, row) => {
                return row?.leave_days_without_permission || "";
            },
        },
        {
            data: null,
            title: "Đánh giá của phòng",
            render: (data, type, row) => {
                return row?.department_rating || "";
            },
        },
    ];
};

const downloadTemplate = async () => {
    const res = await http.get(
        apiWorkTimesheetOvertimeTemplate,
        customDataTableFilterParams
    );
    if (res.data) downloadFileHandler(res.data);
};

const openModalUpload = () => {
    resetFormAfterSubmit(modalUploadForm);
    showModal(modalUpload);
};

modalUploadForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalUploadForm, () => {
        hideModal(modalUpload);
        loadList();
    });
});
