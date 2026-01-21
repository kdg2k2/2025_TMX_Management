const createEditBtn = (
    url,
    color = "warning",
    title = "Cập nhật",
    icon = "ti ti-edit",
) => {
    return createBtn(
        color,
        title,
        false,
        {},
        icon,
        `window.location.href='${url}'`,
    )?.outerHTML;
};

const createViewBtn = (url) => {
    return createBtn(
        "info",
        "Xem",
        false,
        {},
        "ti ti-eye-search",
        `viewFileHandler('${url}')`,
    )?.outerHTML;
};

const createDownloadBtn = (url) => {
    return createBtn(
        "success",
        "Tải",
        false,
        {},
        "ti ti-download",
        `downloadFileHandler('${url}')`,
    )?.outerHTML;
};

const createDetailBtn = (func = null) => {
    return createBtn("info", "Chi tiết", false, {}, "ti ti-list-details", func)
        ?.outerHTML;
};

const createImageGalleryBtn = (func = null) => {
    return createBtn(
        "primary",
        "Quản lý ảnh",
        false,
        {},
        "ti ti-library-photo",
        func,
    )?.outerHTML;
};

const createActionBtn = (
    type = "primary",
    label = "Button",
    url = "#",
    onsuccess = "loadList",
    func = null,
    icon = "",
    attr = {},
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
            ...attr,
        },
        icon,
        onclickHandler,
    )?.outerHTML;
};

const createDeleteBtn = (url, onsuccess = "loadList") =>
    createActionBtn(
        "danger",
        "Xóa",
        url,
        onsuccess,
        "openDeleteModal",
        "ti ti-trash",
    );

const createApproveBtn = (
    url,
    onsuccess = "loadList",
    func = "showApproveModal",
) =>
    createActionBtn(
        "primary",
        "Phê duyệt",
        url,
        onsuccess,
        func,
        "ti ti-check",
    );

const createRejectBtn = (
    url,
    onsuccess = "loadList",
    func = "showRejectModal",
) => createActionBtn("danger", "Từ chối", url, onsuccess, func, "ti ti-x");

const createEditModalBtn = (url, onsuccess = "loadList", func = null) =>
    createActionBtn("warning", "Cập nhật", url, onsuccess, func, "ti ti-edit");
