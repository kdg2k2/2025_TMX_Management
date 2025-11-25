const renderColumns = () => {
    return [
        {
            data: null,
            title: "Kiểu đăng ký",
            render: (data, type, row) => {
                return row?.type?.converted || "";
            },
        },
        {
            data: null,
            title: "Hợp đồng",
            render: (data, type, row) => {
                return row?.contract?.name || "";
            },
        },
        {
            data: null,
            title: "Tên chương trình khác",
            render: (data, type, row) => {
                return row?.other_program_name || "";
            },
        },
        {
            data: null,
            title: "Thời gian dự kiến",
            render: (data, type, row) => {
                return row?.estimated_travel_time || "";
            },
        },
        {
            data: null,
            title: "Điểm khởi hành dự kiến",
            render: (data, type, row) => {
                return row?.expected_departure || "";
            },
        },
        {
            data: null,
            title: "Điểm đến dự kiến",
            render: (data, type, row) => {
                return row?.expected_destination || "";
            },
        },
        {
            data: null,
            className: "text-center",
            title: "Trạng thái",
            render: (data, type, row) => {
                return `<span class="p-1 text-white badge bg-${row.status.color}">${row.status.converted}</span>`;
            },
        },
        {
            data: null,
            title: "Người duyệt",
            render: (data, type, row) => {
                return `
                    <div>
                        <div class="text-center">${
                            row?.approved_by?.name || ""
                        }</div>
                        <div>${
                            row?.approval_note || row?.rejection_note || ""
                        }</div>
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Người đăng ký",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Thời gian tạo",
            render: (data, type, row) => {
                return row.created_at || "";
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
                        row.status.original == "pending_approval"
                            ? createApproveBtn("") + createRejectBtn("")
                            : ""
                    }
                `;
            },
        },
    ];
};
