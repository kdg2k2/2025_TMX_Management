const iframeSyntheticExcel = document.getElementById("iframe-synthetic-excel");
const downloadBtn = document.getElementById("download-btn");
var syntheticExcelUrl = null;

const createExcel = async () => {
    const res = await http.get(synctheticExcelUrl, {}, null, true);
    if (res.data) {
        syntheticExcelUrl = res.data;
        iframeSyntheticExcel.setAttribute(
            "src",
            createLinkPreviewFileOnline(syntheticExcelUrl, 1)
        );
    }
};

downloadBtn.addEventListener("click", () => {
    if (!syntheticExcelUrl) {
        alertErr("Tạo file excel trước");
        return;
    }
    downloadFileHandler(syntheticExcelUrl);
});

document.addEventListener("DOMContentLoaded", createExcel);
