if (typeof customDataTableFilterParams === "undefined") {
    var customDataTableFilterParams = {};
}

const createDataTableFilterParams = () => {
    const defaultParams = {
        paginate: 1,
    };

    return { ...defaultParams, ...customDataTableFilterParams };
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
            : () => {}
    );
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
