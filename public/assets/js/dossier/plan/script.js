const navBienban = document.getElementById("dossier-plan-nav-tab-1");

const downloadExcelBtn = document.getElementById("downloadExcelBtn");

const uploadModalBtn = document.getElementById("uploadModalBtn");
const uploadModal = document.getElementById("uploadModal");

const createMinuteModalBtn = document.getElementById("createMinuteModalBtn");
const createMinuteModal = document.getElementById("createMinuteModal");

const downloadMinuteBtn = document.getElementById("downloadMinuteBtn");

const approveModalBtn = document.getElementById("approveModalBtn");
const approveModal = document.getElementById("approveModal");

// tải mẫu
const handleDownloadExcel = async () => {
    const response = await http.get(urlCreateTempExcel);
    if (response.message) downloadFileHandler(response.data);
};

// tải lên excel
const handleUploadModalSubmit = async (event) => {
    event.preventDefault();

    const fileInput = uploadModal.querySelector('input[name="file"]');

    if (!validateFileSelected(fileInput) || !validateContractSelected()) {
        return;
    }

    const formData = createFormDataWithContract(event.target);
    await handleUploadExcel(formData);
};

// tạo biên bản
const handleCreateMinuteSubmit = async (event) => {
    event.preventDefault();

    if (
        !validateCreateMinuteForm(event.target) ||
        !validateContractSelected()
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

// tải xuống biên bản đang xem
const handleDownloadMinute = () => {
    if (!validateContractSelected()) return;

    if (currentPath) {
        downloadFileHandler(currentPath);
    } else {
        alertInfo("Chưa có file biên bản để tải xuống!");
    }
};

// yêu cầu duyệt
const handleApproveModalSubmit = async (event) => {
    event.preventDefault();

    if (!validateContractSelected()) return;

    const formData = createFormDataWithContract(event.target);
    const response = await http.post(urlSendApproveRequest, formData);

    if (response.message) {
        hideModal(approveModal);
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

document.addEventListener("DOMContentLoaded", () => {
    selectYear.addEventListener("change", handleSelectYearChange);
    selectContract.addEventListener("change", handleContractChange);

    downloadExcelBtn.addEventListener("click", handleDownloadExcel);
    downloadMinuteBtn.addEventListener("click", handleDownloadMinute);

    [
        {
            btn: uploadModalBtn,
            modal: uploadModal,
            handlerSubmit: handleUploadModalSubmit,
        },
        {
            btn: createMinuteModalBtn,
            modal: createMinuteModal,
            handlerSubmit: handleCreateMinuteSubmit,
        },
        {
            btn: approveModalBtn,
            modal: approveModal,
            handlerSubmit: handleApproveModalSubmit,
        },
    ].forEach(({ btn, modal, handlerSubmit }) => {
        btn?.addEventListener("click", () => {
            showModal(modal);
        });

        modal?.addEventListener("submit", handlerSubmit);
    });
});
