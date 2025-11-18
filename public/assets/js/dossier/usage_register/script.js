const navBienban = document.getElementById("dossier-usage_register-nav-tab-1");
const createMinuteModal = document.getElementById("create-minute-modal");

const renderTableContent = (data, table) => {
    var html = data.map((value, index) => {
        return `
            <tr>
                <td>${value[0] ?? ""}</td>
                <td>${value[1] ?? ""}</td>
                <td>${value[2] ?? ""}</td>
                <td>${value[3] ?? ""}</td>
                <td>${value[4] ?? ""}</td>
                <td>${value[7] ?? ""}</td>
                <td>${value[5] ?? ""}</td>
                <td>${value[6] ?? ""}</td>
            </tr>
        `;
    });

    destroyDataTable(table);
    table.find("tbody").html(html.join(""));
    initDataTable(table);
};

const displayStatus = (response) => {
    if (!response) {
        clearStatusDisplay();
        displayIframe(null);
        currentPath = null;
        return;
    }

    // Update user info
    if (response?.flag?.user) {
        createdName.innerHTML = `<span class="text-info">${response?.flag?.user.name}</span>`;
    }

    // Update status icons
    setStatusIcon(isHasData, response?.flag?.has_data);
    setStatusIcon(isHasMinute, response?.flag?.minutes?.length > 0);

    // Update minute status
    var minusStatus = "Chưa có biên bản";
    if (response?.flag?.minutes?.length > 0) {
        const latestMinute = response?.flag.minutes?.at(-1);
        minusStatus = latestMinute?.status?.converted ?? "";

        const latestPath =
            latestMinute?.path || latestMinute?.file_path || null;

        currentPath = latestPath;
        displayIframe(createLinkPreviewFileOnline(latestPath, 1));
    }
    isMinusPending.innerHTML = `<span class="text-danger">${minusStatus}</span>`;
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
    const approveModalBody = approveModal.querySelector(".modal-body");
    if (approveModalBody)
        approveModalBody.innerHTML = `
            <div class="form-group">
                <label for="handover_date" class="form-label">Ngày bàn giao</label>
                <input type="date" class="form-control" id="handover_date" name="handover_date" required>
            </div>
        `;
});
