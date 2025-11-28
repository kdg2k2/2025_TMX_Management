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

const createActionBtn = (
    type = "primary",
    label = "Button",
    url = "#",
    onsuccess = "loadList",
    func = null,
    icon = ""
) => {
    // Nếu có func (string tên hàm) → tạo onclick string
    const onclickHandler = func ? `${func}(this)` : "";

    return createBtn(
        type,
        label,
        false,
        {
            "data-href": url,
            "data-onsuccess": onsuccess,
        },
        icon,
        onclickHandler
    )?.outerHTML;
};

const createDeleteBtn = (url, onsuccess = "loadList") =>
    createActionBtn("danger", "Xóa", url, onsuccess, "openDeleteModal", "ti ti-trash");

const createApproveBtn = (url, onsuccess = "loadList", func = null) =>
    createActionBtn(
        "primary",
        "Phê duyệt",
        url,
        onsuccess,
        func,
        "ti ti-check"
    );

const createRejectBtn = (url, onsuccess = "loadList", func = null) =>
    createActionBtn("danger", "Từ chối", url, onsuccess, func, "ti ti-x");
