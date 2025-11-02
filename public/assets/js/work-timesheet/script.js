const year = document.getElementById("year");
const month = document.getElementById("month");
const iframeExcelContainer = document.getElementById("iframe-excel-container");
const iframeExcel = document.getElementById("iframe-excel");
const noneDataContainer = document.getElementById("none-data-container");
const modalUpload = document.getElementById("modal-upload");
const modalUploadForm = modalUpload.querySelector("form");
var excelUrl = "";

const loadAndShowData = async () => {
    const res = await http.get(apiWorkTimesheetData, {
        year: year.value,
        month: month.value,
    });

    if (res.data) {
        iframeExcelContainer.classList.remove("d-none");
        noneDataContainer.classList.add("d-none", true);
        excelUrl = res.data.path;
    } else {
        iframeExcelContainer.classList.add("d-none", true);
        noneDataContainer.classList.remove("d-none");
        excelUrl = "";
    }

    console.log(excelUrl);

    iframeExcel.setAttribute(
        "src",
        excelUrl ? createLinkPreviewFileOnline(excelUrl, 1) : ""
    );
};

const openModalUpload = (btn) => {
    resetFormAfterSubmit(modalUploadForm);
    showModal(modalUpload);
};

const downloadExcel = () => {
    if (!excelUrl) {
        alertInfo("Tạo file excel trước");
        return;
    }
    downloadFileHandler(excelUrl);
};

modalUploadForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalUploadForm, () => {
        hideModal(modalUpload);
        loadAndShowData();
    });
});

[year, month].forEach((item) => {
    item.addEventListener("change", loadAndShowData);
});

document.addEventListener("DOMContentLoaded", () => {
    loadAndShowData();
});
