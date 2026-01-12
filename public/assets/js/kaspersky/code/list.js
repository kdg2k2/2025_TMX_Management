const renderColumns = () => {
    return [
        {
            data: null,
            title: "Mã",
            render: (data, type, row) => {
                return row?.code || "";
            },
        },
        {
            data: null,
            title: "Lượt sử dụng",
            render: (data, type, row) => {
                return `${row?.available_quantity_message} </br> ${createBadge(
                    row?.is_quantity_exceeded?.converted,
                    row?.is_quantity_exceeded?.color,
                    row?.is_quantity_exceeded?.icon
                )}`;
            },
        },
        {
            data: null,
            title: "Hạn sử dụng",
            render: (data, type, row) => {
                return `
                    <span>
                        ${row?.valid_days} ngày
                    </span>
                    ${createBadge(
                        row?.is_expired?.converted,
                        row?.is_expired?.color,
                        row?.is_expired?.icon
                    )}
                    </br>
                    <span>
                        Từ ${row?.started_at || "N/A"} - đến ${
                            row?.expired_at || "N/A"
                        }
                    </br>
                    <span>
                        Còn lại ${row?.remaining_days || "N/A"} ngày
                    </span>`;
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
                    ${row?.path ? createViewBtn(row.path) : ""}
                    ${
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};
