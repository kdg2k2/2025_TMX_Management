const resultSummary = {
    contractorExperience: [],
    eligibility: [],
    softwareOwnership: [],
    implementationPersonnel: [],
    proofContracts: [],
    otherFiles: [],
};

const setResultSummaryValue = (key, value) => {
    resultSummary[key] = value;
};

const initOriginalTable = (
    table,
    listUrl,
    columns = [],
    callbackAfterRender = () => {},
    selectedArrayKey,
    storeUrl,
    type,
    deleteUrl,
    callbackAfterCheckedChange = () => {}
) => {
    createDataTableServerSide(
        table,
        listUrl,
        columns,
        (item) => item,
        {
            paginate: 1,
        },
        callbackAfterRender,
        true,
        (isChecked, rowData, checkbox) => {
            if (rowData)
                handleCheckeChange(
                    resultSummary[selectedArrayKey],
                    isChecked,
                    rowData.id,
                    callbackAfterCheckedChange,
                    storeUrl,
                    type,
                    deleteUrl
                );
        }
    );
};

const initSelectedTable = (
    table,
    url,
    columns = [],
    callbackAfterRender = () => {}
) => {
    createDataTableServerSide(
        table,
        url,
        columns,
        (item) => item,
        {
            paginate: 1,
        },
        callbackAfterRender
    );
};

const storeSelected = async (url, id, type) => {
    await http.post(url, getParamsByType(id, type));
};

const deleteSelected = async (url, id) => {
    await http.delete(`${url}?id=${id}`);
};

const getParamsByType = (id, type) => {
    const params = {
        bidding_id: $data.id || "",
    };

    switch (type) {
        case "bidding_contractor_experiences":
            params["contract_id"] = id;
            params["file_type"] = $("select[name='file_type']").val();
            break;
        case "bidding_eligibility":
            params["eligibility_id"] = id;
            break;
        case "bidding_software_ownerships":
            params["software_ownership_id"] = id;
            break;
        case "bidding_proof_contracts":
            params["proof_contract_id"] = id;
            break;

        default:
            break;
    }

    return params;
};

const handleCheckeChange = async (
    array,
    isChecked,
    id,
    callbackAfterCheckedChange = () => {},
    storeUrl,
    type,
    deleteUrl
) => {
    const index = array.indexOf(id);

    if (isChecked) {
        if (index === -1) {
            array.push(id);
            await storeSelected(storeUrl, id, type);
        }
    } else {
        if (index !== -1) {
            array.splice(index, 1);
            await deleteSelected(deleteUrl, id);
        }
    }

    if (typeof callbackAfterCheckedChange == "function")
        callbackAfterCheckedChange();
};

const findAndChecked = (table, ids = []) => {
    if (ids.length == 0) return;

    table
        .querySelectorAll('tbody input[type="checkbox"]')
        .forEach((element) => {
            if (ids.includes(Number(element.getAttribute("data-id"))))
                element.checked = true;
            else element.checked = false;
        });
};

const handleOriginalTableChangePage = (table, key) => {
    const ids = resultSummary[key];

    if ($.fn.DataTable.isDataTable($(table))) {
        if ($.fn.DataTable.isDataTable($(table))) {
            const dataTable = $(table).DataTable();
            dataTable
                .off("draw.checkboxHandler")
                .on("draw.checkboxHandler", function () {
                    setTimeout(() => {
                        findAndChecked(table, ids);
                    }, 100);
                });
        } else {
            setTimeout(() => {
                handleOriginalTableChangePage(table, key);
            }, 500);
        }
    } else {
        setTimeout(() => {
            handleOriginalTableChangePage(table, key);
        }, 500);
    }
};

const getFormInputsAndSelects = (form, jquery = false) => {
    const elements = form.querySelectorAll(
        "select, input:not([type='hidden'])"
    );
    return jquery ? $(elements) : elements;
};

const reindexRow = (form) => {
    const rows = form.querySelectorAll(".col-12.row");

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
    reindexFunc = () => {},
    getSelectsFunc = () => {},
    getMaxIndexFunc = () => {}
) => {
    destroySumoSelect(getSelectsFunc());

    // Reindex trước khi clone
    reindexFunc();

    const clone = cloneElement.cloneNode(true);
    clone.removeAttribute("id");

    // Lấy index mới (sau khi reindex thì max luôn là dòng cuối)
    const newIndex = getMaxIndexFunc() + 1;

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
        reindexFunc();
    };

    const icon = btn.querySelector("i");
    icon.classList.remove("ti-plus");
    icon.classList.add("ti-trash");

    // Gắn clone trước nút submit
    const submitRow = form.querySelector(".btn-submit-row");
    form.insertBefore(clone, submitRow);

    initSumoSelect(getSelectsFunc());

    return clone;
};

const resetFormRows = (form, cloneRow) => {
    const cloneRowId = cloneRow.id; // Lấy id từ element
    const allRows = form.querySelectorAll(".col-12.row");

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
