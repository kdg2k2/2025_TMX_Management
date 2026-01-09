const renderColumns = () => {
    return [
        {
            data: null,
            title: "Loại văn bản",
            render: (data, type, row) => {
                return row?.official_document_type?.name || "";
            },
        },
        {
            data: null,
            title: "Thuộc chương trình",
            render: (data, type, row) => {
                if (row?.program_type == "contract")
                    return row?.contract?.name || "";
                return row?.other_program_name || "";
            },
        },
        {
            data: null,
            title: "Số, ký hiệu văn bản",
            render: (data, type, row) => {
                return row?.document_number || "";
            },
        },
        {
            data: null,
            title: "Ngày phát hành",
            render: (data, type, row) => {
                return row?.issuing_date || "";
            },
        },
        {
            data: null,
            title: "Ngày đến",
            render: (data, type, row) => {
                return row?.received_date || "";
            },
        },
        {
            data: null,
            title: "Trích yêu nội dung",
            render: (data, type, row) => {
                return row?.content_summary || "";
            },
        },
        {
            data: null,
            title: "Nơi gửi",
            render: (data, type, row) => {
                return row?.sender_address || "";
            },
        },
        {
            data: null,
            title: "Họ tên người ký",
            render: (data, type, row) => {
                return row?.signer_name || "";
            },
        },
        {
            data: null,
            title: "Chức danh người ký",
            render: (data, type, row) => {
                return row?.signer_position || "";
            },
        },
        {
            data: null,
            title: "Họ tên người liên hệ",
            render: (data, type, row) => {
                return row?.contact_person_name || "";
            },
        },
        {
            data: null,
            title: "Địa chỉ người liên hệ",
            render: (data, type, row) => {
                return row?.contact_person_address || "";
            },
        },
        {
            data: null,
            title: "Số điện thoại người liên hệ",
            render: (data, type, row) => {
                return row?.contact_person_phone || "";
            },
        },
        {
            data: null,
            title: "Ghi chú",
            render: (data, type, row) => {
                return row?.notes || "";
            },
        },
        {
            data: null,
            title: "Trạng thái nhiệm vụ",
            render: (data, type, row) => {
                return createBadge(row?.status?.converted, row?.status?.color);
            },
        },
        {
            data: null,
            title: "Người thực hiện nhiệm vụ",
            render: (data, type, row) => {
                return row?.task_assignee?.name || "";
            },
        },
        {
            data: null,
            title: "Thành viên hỗ trợ nhiệm vụ",
            render: (data, type, row) => {
                return row?.incoming_official_document_users
                    ?.map((value, index) => value?.user?.name || "")
                    ?.filter(Boolean)
                    ?.join(", ");
            },
        },
        {
            data: null,
            title: "Hạn hoàn thành nhiệm vụ",
            render: (data, type, row) => {
                return row?.task_completion_deadline || "";
            },
        },
        {
            data: null,
            title: "Ghi chú nhiệm vụ",
            render: (data, type, row) => {
                return row?.task_notes || "";
            },
        },
        {
            data: null,
            title: "Thời gian giao nhiệm vụ",
            render: (data, type, row) => {
                return row?.assign_at || "";
            },
        },
        {
            data: null,
            title: "Thời gian hoàn thành nhiệm vụ",
            render: (data, type, row) => {
                return row?.complete_at || "";
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
                    ${
                        row?.attachment_file
                            ? createViewBtn(row?.attachment_file)
                            : ""
                    }
                    ${
                        row?.status?.original === "new"
                            ? createActionBtn(
                                  "primary",
                                  "Giao nhiệm vụ",
                                  `${assignUrl}?id=${row.id}`,
                                  "loadList",
                                  "showAssignModal",
                                  "ti ti-clipboard-plus"
                              ) +
                              createEditBtn(`${editUrl}?id=${row.id}`) +
                              createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                            : row?.status?.original === "in_progress"
                            ? createActionBtn(
                                  "success",
                                  "Hoàn thành nhiệm vụ",
                                  `${completeUrl}?id=${row.id}`,
                                  "loadList",
                                  "showCompleteModal",
                                  "ti ti-circle-check"
                              )
                            : ""
                    }

                `;
            },
        },
    ];
};
