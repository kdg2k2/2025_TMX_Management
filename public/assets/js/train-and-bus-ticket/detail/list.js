const renderColumns = () => {
    return [
        {
            data: null,
            title: "Loại thành viên",
            render: (data, type, row) => {
                return row?.user_type?.converted || "";
            },
        },
        {
            data: null,
            title: "Tên thành viên",
            render: (data, type, row) => {
                return row?.user?.name || row?.external_user_name || "";
            },
        },
        {
            data: null,
            title: "Ngày khởi hành",
            render: (data, type, row) => {
                return row?.departure_date || "";
            },
        },
        {
            data: null,
            title: "Ngày về",
            render: (data, type, row) => {
                return row?.return_date || "";
            },
        },
        {
            data: null,
            title: "Nơi khởi hành",
            render: (data, type, row) => {
                return row?.departure_place || "";
            },
        },
        {
            data: null,
            title: "Nơi về",
            render: (data, type, row) => {
                return row?.return_place || "";
            },
        },
        {
            data: null,
            title: "Số hiệu tàu xe",
            render: (data, type, row) => {
                return row?.train_number || "";
            },
        },
        {
            data: null,
            title: "Giá vé (vnđ)",
            render: (data, type, row) => {
                return fmNumber(row?.ticket_price);
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.note || "";
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
                        row.ticket_image_path
                            ? createViewBtn(row.ticket_image_path)
                            : ""
                    }
                    ${createEditBtn(`${editUrl}?id=${row.id}`)}
                `;
            },
        },
    ];
};
