const renderColumns = () => {
    return [
        {
            data: null,
            title: "Loại thiết bị",
            render: (data, type, row) => {
                return row?.device?.device_type?.name || "";
            },
        },
        {
            data: null,
            title: "Tên thiết bị",
            render: (data, type, row) => {
                return row?.device?.name || "";
            },
        },
        {
            data: null,
            title: "Ngày mượn",
            render: (data, type, row) => {
                return row?.borrowed_date || "";
            },
        },
        {
            data: null,
            title: "Ngày dự kiến trả",
            render: (data, type, row) => {
                return row?.expected_return_at || "";
            },
        },
        {
            data: null,
            title: "Vị trí sử dụng",
            render: (data, type, row) => {
                return row?.use_location || "";
            },
        },
        {
            data: null,
            title: "Trạng thái",
            render: (data, type, row) => {
                return createBadge(
                    row?.status?.converted,
                    row?.status?.color,
                    row?.status?.icon
                );
            },
        },
        {
            data: null,
            title: "Người mượn",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Trạng thái thiết bị khi trả",
            render: (data, type, row) => {
                return row?.status?.original == "returned"
                    ? createBadge(
                          row?.device_status_return?.converted,
                          row?.device_status_return?.color,
                          row?.device_status_return?.icon
                      )
                    : "";
            },
        },
        {
            data: null,
            title: "Ngày trả",
            render: (data, type, row) => {
                return row?.returned_at || "";
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
                        row.status.original == "pending"
                            ? createApproveBtn(
                                  `${deviceLoanApproveUrl}?id=${row.id}`
                              ) +
                              createRejectBtn(
                                  `${deviceLoanRejectUrl}?id=${row.id}`
                              )
                            : ""
                    }
                    ${
                        row.status.original == "approved"
                            ? createActionBtn(
                                  "primary",
                                  "Trả thiết bị",
                                  `${apiDeviceLoanReturn}?id=${row.id}`,
                                  "loadList",
                                  "showReturnModal",
                                  "ti ti-arrow-back-up"
                              )
                            : ""
                    }
                `;
            },
        },
    ];
};
