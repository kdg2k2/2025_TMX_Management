var allRowClass = ".clone-row.row";

const getFormFields = (form) => {
    return form.querySelectorAll("select, input:not([type='hidden'])");
};

const reindexRow = (form) => {
    const rows = form.querySelectorAll(allRowClass);

    rows.forEach((row, i) => {
        row.querySelectorAll("select[name], input[name]").forEach((sel) => {
            sel.name = sel.name.replace(/\[\d+\]/, `[${i}]`);
        });
    });
};

const getMaxRowIndex = (elements) => {
    const indexes = [];
    elements.forEach((element) => {
        const match = element.name.match(/\[(\d+)\]/);
        if (match) indexes.push(Number(match[1]));
    });
    return indexes.length ? Math.max(...indexes) : -1;
};

const cloneRow = (
    cloneElement,
    form,
    cloneContainer = form.querySelector(".clone-container")
) => {
    if (!cloneContainer) {
        console.warn("cloneRow: cloneContainer rỗng");
        return;
    }

    const currentFormSelects = cloneContainer.querySelectorAll("select");
    destroySumoSelect($(currentFormSelects));

    // Reindex trước khi clone
    reindexRow(form);
    const clone = cloneElement.cloneNode(true);

    clone.removeAttribute("id");

    // Lấy index mới (sau khi reindex thì max luôn là dòng cuối)
    const newIndex = getMaxRowIndex(currentFormSelects) + 1;

    // Cập nhật name
    clone.querySelectorAll("select[name], input[name]").forEach((el) => {
        el.name = el.name.replace(/\[\d+\]/, `[${newIndex}]`);

        // Reset value trừ khi là input hidden
        if (!(el.tagName === "INPUT" && el.type === "hidden")) {
            el.value = "";
        }
    });

    // Cấu hình nút xóa
    const btn = clone.querySelector("button");
    btn.classList.remove("btn-success");
    btn.classList.add("btn-danger");
    btn.setAttribute("title", "Xóa dòng");
    btn.onclick = async (ev) => {
        ev.preventDefault();

        clone.remove();
        // sau khi xóa, reindex lại luôn
        reindexRow(form);
    };

    const icon = btn.querySelector("i");
    icon.classList.remove("ti-plus");
    icon.classList.add("ti-trash");

    // Gắn clone trước node
    cloneContainer.append(clone);

    refreshSumoSelect($(cloneContainer.querySelectorAll("select")));

    return clone;
};

const resetFormRows = (form, cloneRow) => {
    const cloneRowId = cloneRow.id; // Lấy id từ element
    const allRows = form.querySelectorAll(allRowClass);

    // Xóa tất cả row trừ row gốc
    allRows.forEach((row) => {
        if (row.id !== cloneRowId) {
            row.remove();
        }
    });

    // Reset value của row gốc
    cloneRow.querySelectorAll("select[name], input[name]").forEach((el) => {
        // Không reset input hidden (bidding_id)
        if (!(el.tagName === "INPUT" && el.type === "hidden")) {
            el.value = "";
            // Nếu là select thì clear selected
            if (el.tagName === "SELECT") {
                el.selectedIndex = -1;
            }
            // Nếu là input file thì clear
            if (el.tagName === "INPUT" && el.type === "file") {
                el.value = "";
            }
        }
    });

    // Refresh SumoSelect nếu có
    const selects = cloneRow.querySelectorAll("select");
    if (selects.length > 0) {
        refreshSumoSelect($(selects));
    }

    // Reset lại index về [0]
    cloneRow.querySelectorAll("select[name], input[name]").forEach((el) => {
        el.name = el.name.replace(/\[\d+\]/, `[0]`);
    });
};
