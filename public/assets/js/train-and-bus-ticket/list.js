const renderColumns = () => {
    return [
        {
            data: null,
            title: "Kiểu đăng ký",
            render: (data, type, row) => {
                return row?.type?.converted;
            },
        },
        {
            data: null,
            title: "Hợp đồng",
            render: (data, type, row) => {
                return row?.contract?.name;
            },
        },
        {
            data: null,
            title: "Tên chương trình khác",
            render: (data, type, row) => {
                return row?.other_program_name;
            },
        },
        {
            data: null,
            title: "Thời gian dự kiến",
            render: (data, type, row) => {
                return row?.estimated_travel_time;
            },
        },
        {
            data: null,
            title: "Điểm khởi hành dự kiến",
            render: (data, type, row) => {
                return row?.expected_departure;
            },
        },
        {
            data: null,
            title: "Điểm đến dự kiến",
            render: (data, type, row) => {
                return row?.expected_destination;
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
