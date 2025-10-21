const renderColumns = () => {
    return [
        {
            data: null,
            title: "Nhân sự",
            render: (data, type, row) => {
                return row?.personnel?.name;
            },
        },
        {
            data: null,
            title: "Loại file",
            render: (data, type, row) => {
                return row?.type?.name;
            },
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
            title: "Thời gian tạo",
            render: (data, type, row) => {
                return row.created_at;
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
                            "Xem",
                            false,
                            {},
                            "ti ti-eye-search",
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
