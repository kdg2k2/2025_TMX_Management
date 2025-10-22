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
    try {
        return new URL(url);
    } catch (e) {
        const mess = "URL không hợp lệ";
        alertErr(mess);
        throw new Error(mess);
    }
};

const createLinkPreviewFileOnline = (url, type) => {
    if (!url) return null;

    const encodedUrl = encodeURIComponent(url);
    switch (type) {
        case 1:
            return `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}&embedded=true`;
        case 2:
            return `https://mozilla.github.io/pdf.js/web/viewer.html?file=${encodedUrl}`;
        default:
            return null;
    }
};

const viewFileHandler = (url) => {
    try {
        const validUrl = validateUrl(url);
        const pathname = validUrl.pathname;
        const extension = pathname.split(".").pop().toLowerCase();

        if (officeFormats.includes(extension)) {
            window.open(createLinkPreviewFileOnline(url, 1), "_blank");
        } else if (extension === "pdf") {
            window.open(createLinkPreviewFileOnline(url, 2), "_blank");
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
