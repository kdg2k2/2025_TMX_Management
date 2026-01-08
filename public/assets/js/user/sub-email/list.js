customDataTableFilterParams = {
    user_id: $userId,
};

const renderColumns = () => {
    return [
        {
            data: "email",
            title: "Email",
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
                    ${
                        createEditBtn(
                            `${editUrl}?id=${row.id}&user_id=${row.user_id}`
                        ) + createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
