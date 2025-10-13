const renderColumns = () => {
    return [
        {
            data: "year",
            title: "Năm",
        },
        {
            data: "contract_number",
            title: "Số hợp đồng",
        },
        {
            data: "name",
            title: "Tên",
        },
        {
            data: "short_name",
            title: "Tên viết tắt",
        },
        {
            data: null,
            title: "Loại HĐ",
            render: (data, type, row) => {
                return row?.type?.name;
            },
        },
        {
            data: null,
            title: "Chủ đầu tư",
            render: (data, type, row) => {
                return [row?.investor?.name_vi, row?.investor?.name_en].join(' - ');
            },
        },
        {
            data: null,
            title: "Địa điểm",
            render: (data, type, row) => {
                return row?.scopes
                    ?.map((value, index) => value?.province?.name)
                    .join(", ");
            },
        },
        {
            data: "signed_date",
            title: "Ngày ký",
        },
        {
            data: "effective_date",
            title: "Ngày HĐ có hiệu lực",
        },
        {
            data: "end_date",
            title: "Ngày kêt thúc",
        },
        {
            data: "contract_value",
            title: "Giá trị HĐ",
        },
        {
            data: null,
            title: "Trạng thái",
            render: (data, type, row) => {
                return row?.contract_status?.converted;
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
                return renderActionButtons(row);
            },
        },
    ];
};

const renderActionButtons = (row) => {
    return `
        ${createEditBtn(`${editUrl}?id=${row.id}`)}
        ${createDeleteBtn(`${deleteUrl}?id=${row.id}`)}
    `;
};
