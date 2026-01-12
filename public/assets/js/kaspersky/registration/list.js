const renderColumns = () => {
    return [
        {
            data: null,
            title: "Người đăng ký",
            render: (data, type, row) => {
                return row?.created_by?.name || "";
            },
        },
        {
            data: null,
            title: "Kiểu đăng ký",
            render: (data, type, row) => {
                const deviceInfo =
                    "</br>" +
                    [
                        row?.device?.device_type?.name || "",
                        row?.device?.code || "",
                        row?.device?.name || "",
                    ]
                        .filter(Boolean)
                        .join(" - ");
                return (
                    createBadge(
                        row.type.converted,
                        row.type.color,
                        row.type.icon
                    ) +
                    (["company", "both"].includes(row.type.original) &&
                    deviceInfo
                        ? deviceInfo
                        : "")
                );
            },
        },
        {
            data: null,
            title: "Trạng thái",
            render: (data, type, row) => {
                return createBadge(
                    row.status.converted,
                    row.status.color,
                    row.status.icon
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
                                  `${kasperskyRegistrationApproveUrl}?id=${row.id}`,
                                  "loadList",
                                  "showApproveModal"
                              ) +
                              createRejectBtn(
                                  `${kasperskyRegistrationRejectUrl}?id=${row.id}`,
                                  "loadList",
                                  "showRejectModal"
                              )
                            : ""
                    }
                `;
            },
        },
    ];
};
