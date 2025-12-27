const renderColumns = () => {
    return [
        {
            data: null,
            title: "Hình ảnh",
            render: (data, type, row) => {
                return `
                    <div class="lh-1">
                        ${renderCarousel(
                            "carousel-" + row?.device.id,
                            row?.device?.images
                        )}
                    </div>
                `;
            },
        },
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
            title: "Người mượn",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
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
                                  `${deviceLoanApproveUrl}?id=${row.id}`,
                                  "loadList",
                                  "showApproveModal"
                              ) +
                              createRejectBtn(
                                  `${deviceLoanRejectUrl}?id=${row.id}`,
                                  "loadList",
                                  "showRejectModal"
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
                                  null,
                                  "ti ti-x"
                              )
                            : ""
                    }
                `;
            },
        },
    ];
};

const callbackAfterRenderLoadList = () => {
    initGLightbox();
};
