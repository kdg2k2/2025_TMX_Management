window.loadList = () => {
    createDataTableServerSide(table, listUrl, renderColumns(), (item) => item, {
        paginate: 1,
    });
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
