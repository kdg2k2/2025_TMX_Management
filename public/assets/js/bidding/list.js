const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên gói thầu",
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
                        createDetailBtn(
                            `window.location.href="${showUrl}?id=${row.id}"`
                        ) +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
