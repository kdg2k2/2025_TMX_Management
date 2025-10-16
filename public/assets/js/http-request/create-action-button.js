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
