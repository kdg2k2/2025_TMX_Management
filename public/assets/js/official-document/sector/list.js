const renderColumns = () => {
    return [
        {
            data: null,
            title: "Tên loại văn bản",
            render: (data, type, row) => {
                return row?.name || "";
            },
        },
        {
            data: null,
            title: "Mô tả",
            render: (data, type, row) => {
                return row?.description || "";
            },
        },
        {
            data: null,
            title: "Người nhận mail",
            render: (data, type, row) => {
                return row?.users
                    ?.map((value, index) => value?.name || "")
                    ?.filter(Boolean)
                    ?.join(", ");
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
