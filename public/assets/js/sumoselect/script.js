// Lưu trữ timeout và trạng thái cần refresh
let multipleSelectTimeouts = new Map();
let singleSelectTimeouts = new Map();
let needsRefresh = new Set();

const destroySumoSelect = (selector = null) => {
    if (!selector) {
        console.warn("destroySumoSelect: selector rỗng");
        return;
    }

    selector.each(function () {
        if ($(this)[0].sumo) {
            $(this)[0].sumo.unload();
        }
    });
};

const initSumoSelect = (selector = null, placeholder = "Chọn...") => {
    if (!selector) {
        console.warn("initSumoSelect: selector rỗng");
        return;
    }

    selector.SumoSelect({
        okCancelInMulti: false,
        csvDispCount: 1,
        selectAll: true,
        search: true,
        searchText: "Nhập tìm kiếm...",
        placeholder: placeholder,
        captionFormat: "Đã chọn {0} lựa chọn",
        captionFormatAllSelected: "Đã chọn tất cả {0} lựa chọn",
        captionFormatAllSelected: "Tất cả",
        locale: ["Xác nhận", "Hủy", "Chọn tất cả"],
    });

    selector.each((item, element) => {
        const select = $(element);
        const selectId =
            select.attr("id") || select.attr("name") || `select_${item}`;

        if (select.attr("multiple") == "multiple") {
            // Multiple select: Lắng nghe khi change để đánh dấu cần refresh
            select.on("change", function () {
                needsRefresh.add(selectId);
            });

            // Lắng nghe khi popup đóng thì mới refresh
            select.on("sumo:closed", function () {
                if (needsRefresh.has(selectId)) {
                    // Clear timeout cũ nếu có
                    if (multipleSelectTimeouts.has(selectId)) {
                        clearTimeout(multipleSelectTimeouts.get(selectId));
                    }

                    // Tạo timeout mới - đợi 1s rồi mới refresh
                    const timeoutId = setTimeout(() => {
                        refreshSumoSelect(select);
                        needsRefresh.delete(selectId);
                        multipleSelectTimeouts.delete(selectId);
                    }, 1000);

                    // Lưu timeout ID
                    multipleSelectTimeouts.set(selectId, timeoutId);
                }
            });
        } else {
            // Single select: Refresh ngay khi change
            select.on("change", function () {
                const selectId =
                    select.attr("id") ||
                    select.attr("name") ||
                    `select_${item}`;

                // Clear timeout cũ nếu có
                if (singleSelectTimeouts.has(selectId)) {
                    clearTimeout(singleSelectTimeouts.get(selectId));
                }

                // Tạo timeout mới - đợi 500ms rồi mới refresh
                const timeoutId = setTimeout(() => {
                    refreshSumoSelect(select);
                    singleSelectTimeouts.delete(selectId);
                }, 500);

                singleSelectTimeouts.set(selectId, timeoutId);
            });
        }
    });
};

const getSelects = (query) => {
    return $(query ? query : "select").not(".un-sumo");
};

const refreshSumoSelect = (selects = null) => {
    if (!selects) selects = getSelects();
    destroySumoSelect(selects);
    initSumoSelect(selects);
};

const destroySumoAllSelect = () => {
    const selects = getSelects();

    // Clear tất cả timeout đang chờ
    multipleSelectTimeouts.forEach((timeoutId) => {
        clearTimeout(timeoutId);
    });
    multipleSelectTimeouts.clear();
    needsRefresh.clear();

    destroySumoSelect(selects);
};

document.addEventListener("DOMContentLoaded", () => {
    refreshSumoSelect();
});
