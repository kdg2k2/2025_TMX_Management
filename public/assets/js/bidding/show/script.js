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
    callbackAfterCheckedChange
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
    callbackAfterCheckedChange,
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

    if (typeof window[callbackAfterCheckedChange] == "function")
        window[callbackAfterCheckedChange]();
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
