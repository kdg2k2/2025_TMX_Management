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
            title: "Nội dung kiến nghị",
            render: (data, type, row) => {
                return row?.suggested_content || "";
            },
        },
        {
            data: null,
            title: "Hiện trạng",
            render: (data, type, row) => {
                return row?.device_status || "";
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
            title: "Người đăng ký",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Thời gian hoàn thành sửa",
            render: (data, type, row) => {
                return row?.fixed_at || "";
            },
        },
        {
            data: null,
            title: "Kinh phí sửa chữa (vnđ)",
            render: (data, type, row) => {
                return fmNumber(row?.repair_costs);
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
                        row.status.original == "pending"
                            ? createApproveBtn(
                                  `${deviceFixApproveUrl}?id=${row.id}`,
                                  "loadList",
                                  "showApproveModal"
                              ) +
                              createRejectBtn(
                                  `${deviceFixRejectUrl}?id=${row.id}`,
                                  "loadList",
                                  "showRejectModal"
                              )
                            : ""
                    }
                    ${
                        row.status.original == "approved"
                            ? createActionBtn(
                                  "primary",
                                  "Xác nhận đã sửa",
                                  `${apiDeviceFixFixed}?id=${row.id}`,
                                  "loadList",
                                  "showFixedModal",
                                  "ti ti-fire-extinguisher"
                              )
                            : ""
                    }
                `;
            },
        },
    ];
};
