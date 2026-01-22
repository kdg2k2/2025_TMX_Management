if (typeof customDataTableFilterParams === "undefined") {
    var customDataTableFilterParams = {};
}

const createDataTableFilterParams = () => {
    const defaultParams = {
        paginate: 1,
    };

    return { ...defaultParams, ...customDataTableFilterParams };
};

const renderField = (icon, label, value, options = {}) => {
    const {
        color = "muted", // primary, success, danger, warning, info...
        valueClass = "",
        empty = "-",
    } = options;

    return `
        <div class="d-flex align-items-start gap-1 mb-1">
            <i class="ti ti-${icon} text-${color}"></i>
            <small class="text-muted">${label}:</small>
            <span class="${valueClass}">${value || empty}</span>
        </div>
    `;
};

window.loadList = () => {
    const dataTable = $("#datatable") || table;
    createDataTableServerSide(
        dataTable,
        listUrl,
        renderColumns(),
        (item) => item,
        createDataTableFilterParams(),
        typeof callbackAfterRenderLoadList !== "undefined"
            ? callbackAfterRenderLoadList
            : () => {},
    );
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
