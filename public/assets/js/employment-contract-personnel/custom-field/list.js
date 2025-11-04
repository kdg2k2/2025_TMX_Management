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
        {
            data: null,
            title: "Người tạo - Cập nhật",
            render: (data, type, row) => {
                return row?.created_by?.name;
            },
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
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
