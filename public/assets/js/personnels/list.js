window.loadList = () => {
    createDataTableServerSide(table, listUrl, renderColumns(), (item) => item, {
        paginate: 1,
    });
};

const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên nhân sự",
        },
        {
            data: null,
            title: "Đơn vị",
            render: (data, type, row) => {
                return [row?.personnel_unit?.short_name||null, row?.personnel_unit?.name||null].filter(item=>item!=null).join(" - ");
            },
        },
        {
            data: "educational_level",
            title: "Trình độ học vấn",
        },
        {
            data: null,
            title: "Người tạo",
            render: (data, type, row) => {
                return row?.created_by?.name;
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
