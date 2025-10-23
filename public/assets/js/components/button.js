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
    btn.classList.add("mb-1", "me-1", "btn", "btn-sm");
    if (Array.isArray(color)) color.forEach((item) => btn.classList.add(item));
    if (typeof color == "string") btn.classList.add(`btn-${color}`);

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

const createDropdownBtn = (
    color,
    title,
    items = [],
    iconClass = "",
    btnText = "Dropdown",
    attrs = {}
) => {
    // Tạo wrapper div
    const btnGroup = document.createElement("div");
    btnGroup.className = "btn-group mb-1 me-1";
    btnGroup.setAttribute("role", "group");

    // Tạo button dropdown từ createBtn gốc
    const btn = createBtn(color, title, false, {
        ...attrs,
        "data-bs-toggle": "dropdown",
        "aria-expanded": "false"
    }, iconClass, null, btnText);

    // Thêm class dropdown-toggle
    btn.classList.add("dropdown-toggle");

    // Tạo unique ID cho button
    const dropdownId = `btnDropdown-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    btn.id = dropdownId;

    // Tạo dropdown menu
    const dropdownMenu = document.createElement("ul");
    dropdownMenu.className = "dropdown-menu";
    dropdownMenu.setAttribute("aria-labelledby", dropdownId);

    // Thêm items vào dropdown
    items.forEach(item => {
        const li = document.createElement("li");

        if (item.divider) {
            // Divider
            li.innerHTML = '<hr class="dropdown-divider">';
        } else {
            // Dropdown item
            const link = document.createElement("a");
            link.className = "dropdown-item";
            link.href = item.href || "javascript:void(0);";

            if (item.icon) {
                link.innerHTML = `<i class="${item.icon} me-2"></i>${item.text}`;
            } else {
                link.textContent = item.text;
            }

            if (item.onClick) {
                link.addEventListener("click", (e) => {
                    e.preventDefault();
                    item.onClick(e);
                });
            }

            if (item.disabled) {
                link.classList.add("disabled");
            }

            li.appendChild(link);
        }

        dropdownMenu.appendChild(li);
    });

    btnGroup.appendChild(btn);
    btnGroup.appendChild(dropdownMenu);

    return btnGroup;
};
