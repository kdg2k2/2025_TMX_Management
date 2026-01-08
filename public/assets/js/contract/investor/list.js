const renderColumns = () => {
    return [
        {
            data: "name_vi",
            title: "Tên tiếng việt",
        },
        {
            data: "name_en",
            title: "Tên tiếng anh",
        },
        {
            data: "address",
            title: "Địa chỉ",
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
