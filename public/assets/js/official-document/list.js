const renderColumns = () => {
    return [
        {
            data: null,
            title: "Tên văn bản",
            render: (data, type, row) => {
                return row?.name || "";
            },
        },
        {
            data: null,
            title: "Loại lĩnh vực",
            render: (data, type, row) => {
                return row?.official_document_sector?.name || "";
            },
        },
        {
            data: null,
            title: "Người kiểm tra",
            render: (data, type, row) => {
                return row?.reviewed_by?.name || "";
            },
        },
        {
            data: null,
            title: "Người nhận thông tin",
            render: (data, type, row) => {
                return (
                    row?.users
                        ?.map((v, k) => v?.name || "")
                        ?.filter(Boolean)
                        ?.join(", ") || ""
                );
            },
        },
        {
            data: null,
            title: "Kiểu chương trình",
            render: (data, type, row) => {
                return createBadge(
                    row?.program_type?.converted,
                    row?.program_type?.color,
                    row?.program_type?.icon
                );
            },
        },
        {
            data: null,
            title: "Thuộc chương trình",
            render: (data, type, row) => {
                return (
                    row?.incoming_official_document?.name ||
                    row?.contract?.name ||
                    row?.other_program_name ||
                    ""
                );
            },
        },
        {
            data: null,
            title: "Kiểu phát hành",
            render: (data, type, row) => {
                return createBadge(
                    row?.release_type?.converted,
                    row?.release_type?.color,
                    row?.release_type?.icon
                );
            },
        },
        {
            data: null,
            title: "Loại văn bản",
            render: (data, type, row) => {
                return row?.official_document_type?.name || "";
            },
        },
        {
            data: null,
            title: "Ngày dự kiến phát hành",
            render: (data, type, row) => {
                return row?.expected_release_date || "";
            },
        },
        {
            data: null,
            title: "Người ký",
            render: (data, type, row) => {
                return row?.signed_by?.name || "";
            },
        },
        {
            data: null,
            title: "Nơi nhận",
            render: (data, type, row) => {
                return row?.receiver_organization || "";
            },
        },
        {
            data: null,
            title: "Họ tên người nhận trực tiếp",
            render: (data, type, row) => {
                return row?.receiver_name || "";
            },
        },
        {
            data: null,
            title: "Địa chỉ nơi nhận",
            render: (data, type, row) => {
                return row?.receiver_address || "";
            },
        },
        {
            data: null,
            title: "Điện thoại liên hệ nơi nhận",
            render: (data, type, row) => {
                return row?.receiver_phone || "";
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.expected_release_date || "";
            },
        },
        {
            data: null,
            title: "File soạn thảo (docx)",
            render: (data, type, row) => {
                return row?.pending_review_docx_file
                    ? createViewBtn(row?.pending_review_docx_file) +
                          createDownloadBtn(row?.pending_review_docx_file)
                    : "";
            },
        },
        {
            data: null,
            title: "File điều chỉnh (docx)",
            render: (data, type, row) => {
                return row?.revision_docx_file
                    ? createViewBtn(row?.revision_docx_file) +
                          createDownloadBtn(row?.revision_docx_file)
                    : "";
            },
        },
        {
            data: null,
            title: "File nhận xét (docx)",
            render: (data, type, row) => {
                return row?.comment_docx_file
                    ? createViewBtn(row?.comment_docx_file) +
                          createDownloadBtn(row?.comment_docx_file)
                    : "";
            },
        },
        {
            data: null,
            title: "File phê duyệt (docx)",
            render: (data, type, row) => {
                return row?.approve_docx_file
                    ? createViewBtn(row?.approve_docx_file) +
                          createDownloadBtn(row?.approve_docx_file)
                    : "";
            },
        },
        {
            data: null,
            title: "File phát hành (pdf)",
            render: (data, type, row) => {
                return row?.released_pdf_file
                    ? createViewBtn(row?.released_pdf_file) +
                          createDownloadBtn(row?.released_pdf_file)
                    : "";
            },
        },
        createCreatedByAtColumn("Người đề nghị"),
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
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};

const callbackAfterRenderLoadList = () => {
    const table = ($("#datatable") || table).DataTable();
    table.rows().every(function () {
        const data = this.data();
        const rowNode = this.node();

        if (data?.tr_message) {
            const className = `text-${data.tr_color}`;
            $(rowNode).addClass(className);
            $(rowNode).attr("title", data.tr_message);
        }
    });
};
