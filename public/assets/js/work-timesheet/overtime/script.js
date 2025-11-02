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
                return row?.user?.overtime_total_count || "";
            },
        },
        {
            data: null,
            title: "Tổng ngày nghỉ không phép",
            render: (data, type, row) => {
                return row?.user?.leave_days_without_permission || "";
            },
        },
        {
            data: null,
            title: "Đánh giá của phòng",
            render: (data, type, row) => {
                return row?.user?.department_rating || "";
            },
        },
        {
            data: null,
            title: "Thời gian tạo",
            render: (data, type, row) => {
                return row.created_at || "";
            },
        },
    ];
};

const downloadTemplate = async () => {
    const res = await http.get(
        apiWorkTimesheetOvertimeTemplate,
        customDataTableFilterParams
    );
    console.log({ res });
    if (res.data) downloadFileHandler(res.data);
};

const openModalUpload = (btn) => {
    resetFormAfterSubmit(modalUploadForm);
    showModal(modalUpload);
};

modalUploadForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalUploadForm, () => {
        hideModal(modalUpload);
        loadData();
    });
});
