const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên",
        },
        {
            data: "description",
            title: "Mô tả",
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
                    ${createEditBtn(`${editUrl}?id=${row.id}`)}
                    ${createDeleteBtn(`${deleteUrl}?id=${row.id}`)}
                `;
            },
        },
    ];
};
