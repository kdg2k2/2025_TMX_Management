// Lưu trữ timeout và trạng thái cần refresh
let multipleSelectTimeouts = new Map();
let singleSelectTimeouts = new Map();
let needsRefresh = new Set();

const destroySumoSelect = (selector) => {
    selector.each(function () {
        if ($(this)[0].sumo) {
            $(this)[0].sumo.unload();
        }
    });
};

const initSumoSelect = (selector, placeholder = "Chọn...") => {
    // Validate input
    if (!selector || !selector.jquery) {
        console.warn("initSumoSelect: selector phải là jQuery object");
        return;
    }

    if (selector.length === 0) {
        console.warn("initSumoSelect: không tìm thấy element nào");
        return;
    }

    if (typeof selector.SumoSelect !== "function") {
        console.warn("initSumoSelect: SumoSelect plugin chưa được load");
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

const getSelects = () => {
    return $("select");
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
