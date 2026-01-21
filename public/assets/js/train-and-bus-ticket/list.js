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
            title: "Thành viên",
            render: (data, type, row) => {
                return `${row?.details
                    ?.map(
                        (item) =>
                            `<li>${item?.user_type?.converted} - ${
                                item?.user?.name || item?.external_user_name
                            }</li>`
                    )
                    .join("")}`;
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
            title: "Trạng thái",
            render: (data, type, row) => {
                return createBadge(row.status.converted, row.status.color);
            },
        },
        {
            data: null,
            title: "Người duyệt",
            render: (data, type, row) => {
                return row?.approved_by?.name || "";
            },
        },
        {
            data: null,
            title: "Ghi chú duyệt",
            render: (data, type, row) => {
                return row?.approval_note || row?.rejection_note || "";
            },
        },
        {
            data: null,
            title: "Người đăng ký",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
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
                        row.status.original == "approved"
                            ? createDetailBtn(
                                  `window.location.href='${trainAndBusTicketDetailIndex}?id=${row.id}'`
                              )
                            : ""
                    }
                    ${
                        row.status.original == "pending_approval"
                            ? createApproveBtn(
                                  `${trainAndBusTicketApproveUrl}?id=${row.id}`
                              ) +
                              createRejectBtn(
                                  `${trainAndBusTicketRejectUrl}?id=${row.id}`
                              )
                            : ""
                    }
                `;
            },
        },
    ];
};
