const createBadge = (
    content,
    color,
    iconClass = "",
    roundedPill = true,
    textColor = ""
) => {
    return `
        <span class="badge ${roundedPill ? `rounded-pill` : ""} ${
        textColor ? `text-${textColor}` : ""
    } bg-${color}">
            ${
                iconClass
                    ? `<i class="${iconClass} ${content ? "me-1" : ""}"></i>`
                    : ""
            }
            ${content || ""}
        </span>
    `;
};
