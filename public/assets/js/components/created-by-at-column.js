const createCreatedByAtColumn = () => {
    return {
        data: null,
        title: "Người tạo/cập nhật",
        render: (data, type, row) => {
            return row?.created_by?.name || "";
        },
    };
};
