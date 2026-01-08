const renderColumns = () => {
    return [
        {
            data: null,
            title: "Tên loại văn bản",
            render: (data, type, row) => {
                return row?.name;
            },
        },
        {
            data: null,
            title: "Mô tả",
            render: (data, type, row) => {
                return row?.description;
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
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
