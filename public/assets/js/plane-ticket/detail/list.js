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
            title: "Sân bay khởi hành",
            render: (data, type, row) => {
                return row?.departure_airport?.name || "";
            },
        },
        {
            data: null,
            title: "Sân bay đến",
            render: (data, type, row) => {
                return row?.return_airport?.name || "";
            },
        },
        {
            data: null,
            title: "Hãng bay",
            render: (data, type, row) => {
                return row?.airline?.name || "";
            },
        },
        {
            data: null,
            title: "Hạng vé",
            render: (data, type, row) => {
                return row?.plane_ticket_class?.name || "";
            },
        },
        {
            data: null,
            title: "Số cân hành lý ký gửi (kg)",
            render: (data, type, row) => {
                return row?.checked_baggage_allowances || "";
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
