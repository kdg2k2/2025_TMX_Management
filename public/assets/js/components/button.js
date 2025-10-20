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
    btn.classList.add("mb-1", "btn", "btn-sm");
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
