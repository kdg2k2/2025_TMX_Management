const navBienban = document.getElementById("dossier-plan-nav-tab-1");
const createMinuteModal = document.getElementById("create-minute-modal");

// tạo biên bản
const handleCreateMinuteSubmit = async (event) => {
    event.preventDefault();

    if (
        !validateCreateMinuteForm(event.target) ||
        !validateBasicRequirements()
    ) {
        return;
    }

    const formData = createFormDataWithContract(event.target);
    const response = await http.post(urlCreateMinute, formData);

    if (response && (response.success === true || response.message)) {
        hideModal(createMinuteModal);
        loadData();
    }
};

const validateCreateMinuteForm = (form) => {
    const handoverDate = form.querySelector("#handover-date").value;
    const receivedById = form.querySelector("#received-by").value;

    // Validate handover date
    if (!handoverDate) {
        alertWarning("Vui lòng chọn ngày bàn giao!");
        return false;
    }

    // Validate date not in past (optional)
    const today = new Date().toISOString().split("T")[0];
    if (handoverDate < today) {
        alertWarning("Ngày bàn giao không được là ngày trong quá khứ!");
        return false;
    }

    // Validate received by
    if (!receivedById) {
        alertWarning("Vui lòng chọn người nhận!");
        return false;
    }

    // Validate chưa có biên bản hoặc biên bản bị rejected
    const hasMinute = isHasMinute.querySelector("i.fa-circle-check");
    const minuteStatus = isMinusPending.textContent.trim();

    if (hasMinute && minuteStatus.includes(["draft", "rejected"])) {
        alertInfo(`Đã có biên bản với trạng thái: ${minuteStatus}`);
        return false;
    }

    return true;
};

const renderTableContent = (data, table) => {
    var html = data.map((value, index) => {
        return `
            <tr>
                <td>${value?.type?.name ?? ""}</td>
                <td>${value?.province?.name ?? ""}</td>
                <td>${value?.commune?.name ?? ""}</td>
                <td>${value?.unit?.name ?? ""}</td>
                <td class="text-center">${value?.quantity ?? ""}</td>
                <td class="text-center">${value?.estimated_time ?? ""}</td>
                <td>${value?.responsible_user?.name ?? ""}</td>
                <td>${value?.note ?? ""}</td>
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
    if (response.user) {
        createdName.innerHTML = `<span class="text-info">${response.user.name}</span>`;
    }

    // Update status icons
    setStatusIcon(isHasData, response.details?.length > 0);
    setStatusIcon(isHasMinute, response.minutes?.length > 0);

    // Update minute status
    var minusStatus = "Chưa có biên bản";
    if (response?.minutes?.length > 0) {
        const latestMinute = response.minutes?.at(-1);
        minusStatus = latestMinute?.status?.converted ?? "";

        const latestPath =
            latestMinute?.path || latestMinute?.file_path || null;

        currentPath = latestPath;
        displayIframe(latestPath);
    }
    isMinusPending.innerHTML = `<span class="text-danger">${minusStatus}</span>`;
};

if (typeof customArrayHandleModalSubmit !== "undefined")
    customArrayHandleModalSubmit = {
        btn: createMinuteModalBtn,
        modal: createMinuteModal,
        handlerSubmit: handleCreateMinuteSubmit,
    };
