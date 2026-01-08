const renderColumns = () => {
    return [
        {
            data: null,
            title: "Nội dung chính",
            render: (data, type, row) => {
                return row?.main_content;
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
