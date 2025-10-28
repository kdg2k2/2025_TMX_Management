customDataTableFilterParams = {
    user_id: $userId,
};

const renderColumns = () => {
    return [
        {
            data: "email",
            title: "Email",
        },
        {
            data: null,
            title: "Thời gian tạo/cập nhật",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        <li>${row.created_at}</li>
                        <li>${row.updated_at}</li>
                    </ul>
                `;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return `
                    ${
                        createEditBtn(`${editUrl}?id=${row.id}&user_id=${row.user_id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
