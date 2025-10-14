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

const createBtn = (
    color,
    title,
    modal = false,
    attrs = {},
    iconClass = "",
    onClick = null,
    btnText = ""
) => {
    const btn = document.createElement("button");

    // classes
    btn.classList.add("mb-1", "btn", "btn-sm", `btn-${color}`);

    // common attributes
    btn.type = "button";
    btn.setAttribute("data-bs-placement", "top");
    btn.setAttribute("data-bs-toggle", "tooltip");
    btn.setAttribute("title", title);

    // attributes
    if (attrs && typeof attrs === "object") {
        Object.entries(attrs).forEach(([attr, val]) => {
            if (val !== null && val !== undefined && val !== "")
                btn.setAttribute(attr, val);
        });
    }

    // optional icon
    if (iconClass) {
        const icon = document.createElement("i");
        icon.className = iconClass;
        btn.appendChild(icon);
    }

    // optional onClick
    if (onClick) {
        btn.setAttribute("onclick", onClick);
    }

    if (btnText) btn.textContent = btnText;

    return btn;
};
