const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên văn bản",
        },
        {
            data: "unit",
            title: "Đơn vị tính",
        },
        {
            data: "quantity",
            title: "Số lượng",
        },
        {
            data: "quantity_limit",
            title: "Số lượng tối thiểu",
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
