var customDataTableFilterParams = {};

const createDataTableFilterParams = () => {
    const defaultParams = {
        paginate: 1,
    };

    return { ...defaultParams, ...customDataTableFilterParams };
};

window.loadList = () => {
    createDataTableServerSide(
        table,
        listUrl,
        renderColumns(),
        (item) => item,
        createDataTableFilterParams()
    );
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
