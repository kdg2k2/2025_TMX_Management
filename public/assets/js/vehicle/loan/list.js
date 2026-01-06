const renderColumns = () => {
    return [
        {
            data: null,
            title: "Tên phương tiện",
            render: (data, type, row) => {
                return [row?.vehicle?.brand, row?.vehicle?.license_plate]
                    .filter(Boolean)
                    .join(" - ");
            },
        },
        {
            data: null,
            title: "Hình ảnh (trước mượn)",
            render: (data, type, row) => {
                const images = [
                    row?.before_front_image,
                    row?.before_rear_image,
                    row?.before_left_image,
                    row?.before_right_image,
                    row?.before_odometer_image,
                ].filter(Boolean);
                return `
                    <div class="lh-1">
                        ${renderCarousel("carousel-before-" + row.id, images)}
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Hình ảnh (sau mượn)",
            render: (data, type, row) => {
                const images = [
                    row?.return_front_image,
                    row?.return_rear_image,
                    row?.return_left_image,
                    row?.return_right_image,
                    row?.return_odometer_image,
                ].filter(Boolean);
                return `
                    <div class="lh-1">
                        ${renderCarousel("carousel-return-" + row.id, images)}
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Thời gian lấy xe",
            render: (data, type, row) => {
                return row?.vehicle_pickup_time || "";
            },
        },
        {
            data: null,
            title: "Ngày dự kiến trả",
            render: (data, type, row) => {
                return row?.estimated_vehicle_return_date || "";
            },
        },
        {
            data: null,
            title: "Điểm đến",
            render: (data, type, row) => {
                return row?.destination || "";
            },
        },
        {
            data: null,
            title: "Nội dung công việc",
            render: (data, type, row) => {
                return row?.work_content || "";
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
            title: "Số km hiện trạng khi mượn",
            render: (data, type, row) => {
                return fmNumber(row?.current_km);
            },
        },
        {
            data: null,
            title: "Số km hiện trạng khi trả",
            render: (data, type, row) => {
                return fmNumber(row?.return_km);
            },
        },
        {
            data: null,
            title: "Trạng thái phương tiện khi trả",
            render: (data, type, row) => {
                return row?.status?.original == "returned"
                    ? createBadge(
                          row?.vehicle_status_return?.converted,
                          row?.vehicle_status_return?.color,
                          row?.vehicle_status_return?.icon
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
        {
            data: null,
            title: "Chi phí đổ xăng",
            render: (data, type, row) => {
                return fmNumber(row?.fuel_cost);
            },
        },
        {
            data: null,
            title: "Người trả chi phí đổ xăng(vnđ)",
            render: (data, type, row) => {
                return row?.fuel_cost_paid_by?.name || "";
            },
        },
        {
            data: null,
            title: "Chi phí bảo dưỡng",
            render: (data, type, row) => {
                return fmNumber(row?.maintenance_cost);
            },
        },
        {
            data: null,
            title: "Người trả chi phí bảo dưỡng(vnđ)",
            render: (data, type, row) => {
                return row?.maintenance_cost_paid_by?.name || "";
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
                                  `${vehicleLoanApproveUrl}?id=${row.id}`,
                                  "loadList",
                                  "showApproveModal"
                              ) +
                              createRejectBtn(
                                  `${vehicleLoanRejectUrl}?id=${row.id}`,
                                  "loadList",
                                  "showRejectModal"
                              )
                            : ""
                    }
                    ${
                        row.status.original == "approved" && !row?.returned_at
                            ? createActionBtn(
                                  "primary",
                                  "Trả phương tiện",
                                  `${apiVehicleLoanReturn}?id=${row.id}`,
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

const callbackAfterRenderLoadList = () => {
    initGLightbox();
};
