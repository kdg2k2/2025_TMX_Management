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
            title: "Tuần",
            render: (data, type, row) => {
                return row?.week;
            },
        },
        {
            data: null,
            title: "Nội dung chính",
            render: (data, type, row) => {
                return row?.main_content;
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
