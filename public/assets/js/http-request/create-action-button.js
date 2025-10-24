const createEditBtn = (url) => {
    return createBtn(
        "warning",
        "Cập nhật",
        false,
        {},
        "ti ti-edit",
        `window.location.href='${url}'`
    )?.outerHTML;
};

const createDeleteBtn = (url, onSuccess = "loadList") => {
    return createBtn(
        "danger",
        "Xóa",
        false,
        {
            "data-href": url,
            "data-onsuccess": onSuccess,
        },
        "ti ti-trash",
        "openDeleteModal(this)"
    )?.outerHTML;
};

const createViewBtn = (url) => {
    return createBtn(
        "info",
        "Xem",
        false,
        {},
        "ti ti-eye-search",
        `viewFileHandler('${url}')`
    )?.outerHTML;
};

const createDownloadBtn = (url) => {
    return createBtn(
        "success",
        "Tải",
        false,
        {},
        "ti ti-download",
        `downloadFileHandler('${url}')`
    )?.outerHTML;
};
