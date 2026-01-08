const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên nhân sự",
        },
        {
            data: null,
            title: "Đơn vị",
            render: (data, type, row) => {
                return [
                    row?.personnel_unit?.short_name || null,
                    row?.personnel_unit?.name || null,
                ]
                    .filter((item) => item != null)
                    .join(" - ");
            },
        },
        {
            data: "educational_level",
            title: "Trình độ học vấn",
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
