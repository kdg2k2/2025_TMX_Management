const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên gói thầu",
        },
        {
            data: null,
            title: "Người tạo - Cập nhật",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
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
