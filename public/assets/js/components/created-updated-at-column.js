const createCreatedUpdatedColumn = () => {
    return {
        data: null,
        title: "Thời gian tạo/cập nhật",
        render: (data, type, row) => {
            return `
                <ul class="m-0 list-unstyled">
                    ${
                        row.created_at
                            ? `<li>
                                    <i class="ti ti-calendar-event me-1"></i>
                                    ${row.created_at}
                            </li>`
                            : ""
                    }
                    ${
                        row.updated_at
                            ? `<li>
                                    <i class="ti ti-clock me-1"></i>
                                    ${row.updated_at}
                            </li>`
                            : ""
                    }
                </ul>
            `;
        },
    };
};
