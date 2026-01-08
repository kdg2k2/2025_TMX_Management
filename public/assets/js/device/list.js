const renderColumns = () => {
    return [
        {
            data: null,
            title: "Hình ảnh",
            render: (data, type, row) => {
                return `
                    <div class="lh-1">
                        ${renderCarousel("carousel-" + row.id, row?.images)}
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Loại thiết bị",
            render: (data, type, row) => {
                return row?.device_type?.name || "";
            },
        },
        {
            data: null,
            title: "Tên thiết bị",
            render: (data, type, row) => {
                return row?.name || "";
            },
        },
        {
            data: null,
            title: "Mã thiết bị (hệ thống)",
            render: (data, type, row) => {
                return row.code;
            },
        },
        {
            data: null,
            title: "Seri",
            render: (data, type, row) => {
                return row?.seri || "";
            },
        },
        {
            data: null,
            title: "Trạng thái",
            render: (data, type, row) => {
                return createBadge(
                    row?.current_status?.converted,
                    row?.current_status?.color,
                    row?.current_status?.icon
                );
            },
        },
        {
            data: null,
            title: "Vị trí hiện tại",
            render: (data, type, row) => {
                return row?.current_location || "";
            },
        },
        {
            data: null,
            title: "Người sử dụng",
            render: (data, type, row) => {
                return row?.user?.name || "";
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
                        createImageGalleryBtn(
                            `window.location.href='${deviceImageIndex}?device_id=${row.id}'`
                        ) +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};

const callbackAfterRenderLoadList = () => {
    initGLightbox();
};
