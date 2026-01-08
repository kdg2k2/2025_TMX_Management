const renderColumns = () => {
    return [
        {
            data: "z_index",
            title: "Độ ưu tiên",
        },
        {
            data: "name",
            title: "Tên hiển thị",
        },
        {
            data: "field",
            title: "Tên lưu csdl",
        },
        {
            data: null,
            title: "Định dạng",
            render: (data, type, row) => {
                return row?.type?.converted;
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
