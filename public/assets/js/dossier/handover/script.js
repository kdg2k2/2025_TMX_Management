const navBienban = document.getElementById("dossier-handover-nav-tab-1");
const handoverInCount = document.getElementById("handover-in-count");

const handleCreateMinuteClick = async (event) => {
    event.preventDefault();

    if (!validateBasicRequirements()) return;

    const response = await http.get(urlCreateMinute, {
        contract_id: currentContractId,
    });

    if (response && response.message) loadData();
};

const renderTableContent = (data, table) => {
    var html = data?.map((value, index) => {
        return `
            <tr>
                <td>${value?.type?.name ?? ""}</td>
                <td>${value?.plan_province?.name ?? ""}</td>
                <td>${value?.plan_commune?.name ?? ""}</td>
                <td>${value?.plan_unit?.name ?? ""}</td>
                <td>${value?.handover_province?.name ?? ""}</td>
                <td>${value?.handover_commune?.name ?? ""}</td>
                <td>${value?.handover_unit?.name ?? ""}</td>
                <td class="text-center">${value?.plan ?? ""}</td>
                <td class="text-center">${value?.approved ?? ""}</td>
                <td class="text-center">${value?.waiting_approve ?? ""}</td>
                <td>${value?.note ?? ""}</td>
            </tr>
        `;
    });

    destroyDataTable(table);
    table.find("tbody").html(html.join(""));
    initDataTable(table);
};

const customClearStatusDisplay = () => {
    handoverInCount.innerHTML = "";
};

const displayStatus = (response) => {
    if (!response) {
        clearStatusDisplay();
        displayIframe(null);
        currentPath = null;
        return;
    }

    // Update user info
    if (response?.flag?.user)
        createdName.innerHTML = `<span class="text-info">${response?.flag?.user?.name}</span>`;

    // Update status icons
    setStatusIcon(isHasData, response?.flag?.has_data);
    setStatusIcon(isHasMinute, response?.flag?.minutes?.length > 0);

    // Update minute status
    var minusStatus = "Chưa có biên bản";
    if (response?.flag?.minutes?.length > 0) {
        const latestMinute = response?.flag?.minutes?.at(-1);
        minusStatus = latestMinute?.status?.converted ?? "";

        const latestPath =
            latestMinute?.path || latestMinute?.file_path || null;

        currentPath = latestPath;
        displayIframe(latestPath);
    }
    isMinusPending.innerHTML = `<span class="text-danger">${minusStatus}</span>`;

    handoverInCount.innerHTML = response?.flag?.times;
};

const customValidateHandleDownloadExcel = () => {
    return validateContractSelected();
};

const customParamsHandleDownloadExcel = () => {
    return {
        contract_id: currentContractId,
    };
};

document.addEventListener("DOMContentLoaded", () => {
    createMinuteModalBtn.addEventListener("click", handleCreateMinuteClick);
});
