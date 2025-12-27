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
    createDataTableServerSide(
        $("#datatable") || table,
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
