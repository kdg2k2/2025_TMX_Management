const renderColumns = () => {
    return [
        {
            data: null,
            title: "Ngày họp",
            render: (data, type, row) => {
                return row?.meeting_day;
            },
        },
        {
            data: null,
            title: "Số",
            render: (data, type, row) => {
                return row?.number;
            },
        },
        {
            data: null,
            title: "Nội dung chính",
            render: (data, type, row) => {
                return row?.main_content;
            },
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
                        createViewBtn(row.path) +
                        createDownloadBtn(row.path) +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
