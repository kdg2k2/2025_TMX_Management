window.loadList = () => {
    createDataTableServerSide(table, listUrl, renderColumns(), (item) => item, {
        paginate: 1,
    });
};

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

const createDeleteBtn = (url) => {
    return createBtn(
        "danger",
        "Xóa",
        true,
        {
            "data-bs-target": "#modalDelete",
            "data-bs-toggle": "modal",
            "data-href": url,
            "data-onsuccess": "loadList",
        },
        "ti ti-trash"
    )?.outerHTML;
};

const createBtn = (
    color,
    title,
    modal = false,
    modalAttrs = {},
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

    // modal attributes
    if (modal && modalAttrs && typeof modalAttrs === "object") {
        Object.entries(modalAttrs).forEach(([attr, val]) => {
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

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
