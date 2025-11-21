const navBienban = document.getElementById(
    "professional-record-plan-nav-tab-1"
);

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
        displayIframe(createLinkPreviewFileOnline(latestPath, 1));
    }
    isMinusPending.innerHTML = `<span class="text-danger">${minusStatus}</span>`;
};
