const year = document.getElementById("year");
const month = document.getElementById("month");
const iframeExcelContainer = document.getElementById("iframe-excel-container");
const iframeExcel = document.getElementById("iframe-excel");
const noneDataContainer = document.getElementById("none-data-container");
var excelUrl = "";

const loadAndShowData = async () => {
    const res = await http.get(apiWorkTimesheetData, {
        year: year.value,
        month: month.value,
    });

    if (res?.data?.payroll_path) {
        iframeExcelContainer.classList.remove("d-none");
        noneDataContainer.classList.add("d-none", true);
        excelUrl = res.data.payroll_path;
    } else {
        iframeExcelContainer.classList.add("d-none", true);
        noneDataContainer.classList.remove("d-none");
        excelUrl = "";
    }

    iframeExcel.setAttribute(
        "src",
        excelUrl ? createLinkPreviewFileOnline(excelUrl, 1) : ""
    );
};

const downloadExcel = () => {
    if (!excelUrl) {
        alertInfo("Không có dữ liệu");
        return;
    }
    downloadFileHandler(excelUrl);
};

const afterSubmitFromHandle = (form) => {
    hideModal(form);
    loadAndShowData();
};

[year, month].forEach((item) => {
    item.addEventListener("change", loadAndShowData);
});

document.addEventListener("DOMContentLoaded", () => {
    loadAndShowData();
});
