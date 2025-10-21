// Danh sách định dạng Office
const officeFormats = [
    "doc",
    "docx",
    "docm",
    "dot",
    "dotx",
    "dotm",
    "xls",
    "xlsx",
    "xlsm",
    "xlsb",
    "xlt",
    "xltx",
    "xltm",
    "ppt",
    "pptx",
    "pptm",
    "pps",
    "ppsx",
    "ppsm",
    "pot",
    "potx",
    "potm",
];

const validateUrl = (url) => {
    // Kiểm tra URL hợp lệ
    let validUrl;
    try {
        return new URL(url);
    } catch (e) {
        const mess = "URL không hợp lệ";
        alertErr(mess);
        throw new Error(mess);
    }
};

const viewFileHandler = (url) => {
    try {
        const validUrl = validateUrl(url);
        const pathname = validUrl.pathname;
        const extension = pathname.split(".").pop().toLowerCase();

        if (officeFormats.includes(extension)) {
            const encodedUrl = encodeURIComponent(url);
            const previewUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}&embedded=true`;
            window.open(previewUrl, "_blank");
        } else if (extension === "pdf") {
            const encodedUrl = encodeURIComponent(url);
            const previewUrl = `https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodedUrl}`;
            window.open(previewUrl, "_blank");
        } else {
            downloadFile(url);
        }
    } catch (error) {
        console.error("Lỗi xử lý file:", error);
    }
};

const downloadFileHandler = (url) => {
    const validUrl = validateUrl(url);
    const pathname = validUrl.pathname;
    const a = document.createElement("a");

    a.href = url;
    a.download = pathname.split("/").pop() || "download";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
};
