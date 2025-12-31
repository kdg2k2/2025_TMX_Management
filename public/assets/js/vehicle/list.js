const renderColumns = () => {
    return [
        {
            data: null,
            title: "Hãng xe",
            render: (data, type, row) => {
                return row?.brand || "";
            },
        },
        {
            data: null,
            title: "Biển số xe",
            render: (data, type, row) => {
                return row?.license_plate || "";
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
            title: "Điểm đến",
            render: (data, type, row) => {
                return row?.destination || "";
            },
        },
        {
            data: null,
            title: "Người sử dụng",
            render: (data, type, row) => {
                return row?.user?.name || "";
            },
        },
        {
            data: null,
            title: "Số km hiện trạng",
            render: (data, type, row) => {
                return fmNumber(row?.current_km);
            },
        },
        {
            data: null,
            title: "Số km đến hạn bảo dưỡng",
            render: (data, type, row) => {
                return fmNumber(row?.maintenance_km);
            },
        },
        {
            data: null,
            title: "Hạn đăng kiểm",
            render: (data, type, row) => {
                return row?.inspection_expired_at || "";
            },
        },
        {
            data: null,
            title: "Hạn bảo hiểm trách nhiệm dân sự",
            render: (data, type, row) => {
                return row?.liability_insurance_expired_at || "";
            },
        },
        {
            data: null,
            title: "Hạn bảo hiểm thân vỏ",
            render: (data, type, row) => {
                return row?.body_insurance_expired_at || "";
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
