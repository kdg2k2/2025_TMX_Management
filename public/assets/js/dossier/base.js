const selectYear = document.getElementById("year");
const selectContract = document.getElementById("contract-id");
const countContract = document.querySelector('label[for="contract-id"] span');
const tableDulieu = $("#table-dulieu");
const createdName = document.getElementById("created-name");
const isHasData = document.getElementById("is-has-data");
const isHasMinute = document.getElementById("is-has-minute");
const isMinusPending = document.getElementById("is-minus-pending");
const downloadExcelBtn = document.getElementById("download-excel-btn");
const uploadModalBtn = document.getElementById("upload-modal-btn");
const uploadModal = document.getElementById("upload-modal");
if ($showCreateMinuteBtn)
    var createMinuteModalBtn = document.getElementById(
        "create-minute-modal-btn"
    );
const downloadMinuteBtn = document.getElementById("download-minute-btn");
const approveModalBtn = document.getElementById("approve-modal-btn");
const approveModal = document.getElementById("approve-modal");
var currentContractId = null;
var currentPath = null;
var customArrayHandleModalSubmit = {};

// Validate contract đã được chọn
const validateContractSelected = () => {
    if (!currentContractId) {
        alertWarning("Vui lòng chọn hợp đồng!");
        return false;
    }
    return true;
};

// Validate có dữ liệu
const validateHasData = () => {
    const hasData = isHasData.querySelector("i.ti-circle-check");
    if (!hasData) {
        alertWarning("Chưa có dữ liệu!");
        return false;
    }
    return true;
};

// Validate có biên bản
const validateHasMinute = () => {
    const hasMinute = isHasMinute.querySelector("i.ti-circle-check");
    if (!hasMinute) {
        alertWarning("Chưa có biên bản!");
        return false;
    }
    return true;
};

// Validate tổng hợp cho các action cần contract + data
const validateBasicRequirements = () => {
    return validateContractSelected() && validateHasData();
};

// Validate tổng hợp cho các action cần contract + data + minute
const validateFullRequirements = () => {
    return (
        validateContractSelected() && validateHasData() && validateHasMinute()
    );
};

// Validate file đã được chọn
const validateFileSelected = (fileInput) => {
    if (!fileInput.files[0]) {
        alertWarning("Vui lòng chọn file!");
        return false;
    }
    return true;
};

// chèn thêm contract_id vào form
const createFormDataWithContract = (form) => {
    const formData = new FormData(form);
    formData.append("contract_id", currentContractId);
    return formData;
};

const setStatusIcon = (element, hasData) => {
    element.innerHTML = hasData
        ? '<i class="ti ti-circle-check text-success"></i>'
        : '<i class="ti ti-xbox-x text-danger"></i>';
};

const clearStatusDisplay = () => {
    createdName.innerHTML = "";
    setStatusIcon(isHasData, false);
    setStatusIcon(isHasMinute, false);
    isMinusPending.innerHTML = "";

    if (typeof customClearStatusDisplay == "function")
        customClearStatusDisplay();
};

const displayIframe = (path) => {
    if (!path) {
        navBienban.innerHTML = `
            <div class="text-center">
                <h4 class="m-0">Chưa có biên bản nào được tạo.</h4>
            </div>
        `;
    } else {
        const pdfUrl = `${path}#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&scrollbar=0`;

        navBienban.innerHTML = `<iframe src="${pdfUrl}" frameborder="0" class="w-100 mt-3" style="height:82vh;" scrolling="auto"></iframe>`;

        const el = document.querySelector(
            `a[href="#${navBienban.getAttribute("id")}"]`
        );
        if (el) el.click();
    }
};

const handleSelectYearChange = (e) => {
    const value = e.target.value;

    const filteredContracts = $contracts.filter(
        (contract) => contract.year == value
    );

    selectContract.innerHTML = "";
    selectContract.append(new Option("Chọn hợp đồng", ""));
    filteredContracts.forEach((contract) => {
        selectContract.append(new Option(contract.name, contract.id));
    });

    countContract.textContent = `Tổng ${filteredContracts.length}`;
    refreshSumoSelect($(selectContract));

    currentContractId = null;
    loadData();
};

const handleContractChange = (e) => {
    currentContractId = e.target.value;
    loadData();
};

const loadData = async () => {
    displayStatus(null);
    displayTable(null);
    if (currentContractId) {
        try {
            const response = await http.get(
                `${urlLoadData}?year=${selectYear.value}&contract_id=${currentContractId}`
            );
            displayStatus(response ?? null);
            displayTable((response?.details || response?.comparison || response?.registerMerged) ?? null);
        } catch (error) {
            console.error("Load data failed:", error);
        }
    }
};

const displayTable = (data) => {
    if (!data) {
        console.warn("Data is null or undefined");
        destroyDataTable(tableDulieu);
        tableDulieu.find("tbody").html("");
        initDataTable(tableDulieu);
        return;
    }

    if (typeof data === "object" && data !== null && !Array.isArray(data)) {
        data = Object.values(data);
    }

    if (!Array.isArray(data) || data.length === 0) {
        console.warn("Data is not a valid array or is empty");
        destroyDataTable(tableDulieu);
        tableDulieu.find("tbody").html("");
        initDataTable(tableDulieu);
        return;
    }

    renderTableContent(data, tableDulieu);
};

const handleModalSubmitBase = async ({ e, validate, url, modal }) => {
    e.preventDefault();
    if (!validate()) return;

    const form = e.target;
    form.setAttribute("action", url);

    await handleSubmitForm(
        e,
        () => {
            loadData();
            hideModal(modal);
        },
        true,
        createFormDataWithContract(form)
    );
};

// tải mẫu
const handleDownloadExcel = async () => {
    if (typeof customValidateHandleDownloadExcel == "function")
        if (!customValidateHandleDownloadExcel()) return;

    let params = {};
    if (typeof customParamsHandleDownloadExcel == "function")
        params = customParamsHandleDownloadExcel();

    const response = await http.get(urlCreateTempExcel, params);
    if (response.message) downloadFileHandler(response.data);
};

// tải lên excel
const handleUploadModalSubmit = (e) =>
    handleModalSubmitBase({
        e,
        validate: () =>
            validateFileSelected(
                uploadModal.querySelector('input[name="file"]')
            ) && validateContractSelected(),
        url: urlUploadExcel,
        modal: uploadModal,
    });

// tải xuống biên bản đang xem
const handleDownloadMinute = () => {
    if (!validateFullRequirements()) return;

    if (currentPath) {
        downloadFileHandler(currentPath);
    } else {
        alertInfo("Chưa có file biên bản để tải xuống!");
    }
};

// yêu cầu duyệt
const handleApproveModalSubmit = (e) =>
    handleModalSubmitBase({
        e,
        validate: validateFullRequirements,
        url: urlSendApproveRequest,
        modal: approveModal,
    });

const getArrayHandleModalSubmit = () => {
    let arr = [
        {
            btn: uploadModalBtn,
            modal: uploadModal,
            handlerSubmit: handleUploadModalSubmit,
        },
        {
            btn: approveModalBtn,
            modal: approveModal,
            handlerSubmit: handleApproveModalSubmit,
        },
    ];

    if (
        typeof customArrayHandleModalSubmit !== "undefined" &&
        typeof customArrayHandleModalSubmit == "object"
    )
        arr.push(customArrayHandleModalSubmit);

    return arr;
};

document.addEventListener("DOMContentLoaded", () => {
    selectYear.addEventListener("change", handleSelectYearChange);
    selectContract.addEventListener("change", handleContractChange);
    downloadExcelBtn.addEventListener("click", handleDownloadExcel);
    downloadMinuteBtn.addEventListener("click", handleDownloadMinute);

    getArrayHandleModalSubmit().forEach(({ btn, modal, handlerSubmit }) => {
        btn?.addEventListener("click", () => {
            showModal(modal);
        });
        modal?.addEventListener("submit", handlerSubmit);
    });
});
