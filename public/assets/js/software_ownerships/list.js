const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên văn bản",
        },
        {
            data: null,
            title: "Người tạo",
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
                        createBtn(
                            "info",
                            "Xem file",
                            false,
                            {},
                            "ti ti-file-type-pdf",
                            `viewFileHandler('${row.path}')`
                        )?.outerHTML +
                        createBtn(
                            "success",
                            "Tải",
                            false,
                            {},
                            "ti ti-download",
                            `downloadFileHandler('${row.path}')`
                        )?.outerHTML +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
