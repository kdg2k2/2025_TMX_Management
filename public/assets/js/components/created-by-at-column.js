const createCreatedByAtColumn = (
    title = "Người tạo/cập nhật",
    fieldInfo = "created_by"
) => {
    return {
        data: null,
        title: title,
        render: (data, type, row) => {
            return row?.[fieldInfo]?.name || "";
        },
    };
};
