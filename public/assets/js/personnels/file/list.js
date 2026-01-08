const typeFilter = document.getElementById("type_id");
const personnelFilter = document.getElementById("personnel_id");

const renderColumns = () => {
    return [
        {
            data: null,
            title: "Nhân sự",
            render: (data, type, row) => {
                return row?.personnel?.name;
            },
        },
        {
            data: null,
            title: "Loại file",
            render: (data, type, row) => {
                return row?.type?.name;
            },
        },
        createCreatedByAtColumn(),
        createCreatedUpdatedColumn(),
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return `
                    ${
                        createViewBtn(row.path) +
                        createDownloadBtn(row.path) +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};

const getListParams = () => {
    const params = {
        paginate: 1,
    };

    if (typeFilter.value) params["type_id"] = typeFilter.value;
    if (personnelFilter.value) params["personnel_id"] = personnelFilter.value;

    return params;
};

window.loadList = () => {
    createDataTableServerSide(
        table,
        listUrl,
        renderColumns(),
        (item) => item,
        getListParams()
    );
};

document.addEventListener("DOMContentLoaded", () => {
    loadList();

    [typeFilter, personnelFilter].forEach((item) =>
        item.addEventListener("change", loadList)
    );
});
