const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Loại file",
        },
        {
            data: "description",
            title: "Mô tả",
        },
        {
            data: null,
            title: "Cho phép upload",
            render: (data, type, row) => {
                if (row.type == "url") return "Url";
                return `
                    ${row?.extensions
                        ?.map(
                            (value, index) => `${value?.extension?.extension}`
                        )
                        .join(", ")}
                `;
            },
        },
        createCreatedUpdatedColumn(),
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return `
                    ${createEditBtn(`${editUrl}?id=${row.id}`)}
                    ${createDeleteBtn(`${deleteUrl}?id=${row.id}`)}
                `;
            },
        },
    ];
};
