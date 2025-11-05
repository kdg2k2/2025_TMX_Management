const selectYear = document.getElementById("nam");
const selectContract = document.getElementById("contract_id");
const countContract = document.querySelector('label[for="contract_id"] span');
const tableDulieu = $("#table-dulieu");
const createdName = document.getElementById("createdName");
const isHasData = document.getElementById("isHasData");
const isHasMinute = document.getElementById("isHasMinute");
const isMinusPending = document.getElementById("isMinusPending");

// storage
var currentContractId = null;
var currentPath = null;

/**
 * Validate contract đã được chọn
 */
const validateContractSelected = () => {
    if (!currentContractId) {
        alertWarning("Vui lòng chọn hợp đồng!");
        return false;
    }
    return true;
};

/**
 * Validate có dữ liệu
 */
const validateHasData = () => {
    const hasData = isHasData.querySelector("i.fa-circle-check");
    if (!hasData) {
        alertWarning("Chưa có dữ liệu!");
        return false;
    }
    return true;
};

/**
 * Validate có biên bản
 */
const validateHasMinute = () => {
    const hasMinute = isHasMinute.querySelector("i.fa-circle-check");
    if (!hasMinute) {
        alertWarning("Chưa có biên bản!");
        return false;
    }
    return true;
};

/**
 * Validate tổng hợp cho các action cần contract + data
 */
const validateBasicRequirements = () => {
    return validateContractSelected() && validateHasData();
};

/**
 * Validate tổng hợp cho các action cần contract + data + minute
 */
const validateFullRequirements = () => {
    return (
        validateContractSelected() && validateHasData() && validateHasMinute()
    );
};

/**
 * Validate cho approve action
 */
const validateApproveRequirements = () => {
    return (
        validateContractSelected() && validateHasData() && validateHasMinute()
    );
};

/**
 * Validate file đã được chọn
 */
const validateFileSelected = (fileInput) => {
    if (!fileInput.files[0]) {
        alertWarning("Vui lòng chọn file!");
        return false;
    }
    return true;
};

/**
 * Create FormData with common fields
 */
const createFormDataWithContract = (form) => {
    const formData = new FormData(form);
    formData.append("contract_id", currentContractId);
    return formData;
};

/**
 * Set status icon
 */
const setStatusIcon = (element, hasData) => {
    element.innerHTML = hasData
        ? '<i class="fa-regular fa-circle-check text-success"></i>'
        : '<i class="fa-regular fa-circle-xmark text-danger"></i>';
};

/**
 * Clear all status displays
 */
const clearStatusDisplay = () => {
    createdName.innerHTML = "";
    setStatusIcon(isHasData, false);
    setStatusIcon(isHasMinute, false);
    isMinusPending.innerHTML = "";
};

/**
 * upload excel handler
 */
const handleUploadExcel = async (formData) => {
    try {
        const data = await http.post(urlUploadExcel, formData);

        if (data && (data.success === true || data.message)) {
            loadData();
            hideModal(uploadModal);
        }
    } catch (err) {
        console.error("handleUploadExcel failed", err);
    }
};

/**
 * display status
 */
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
        $(navBienban).trigger("click");
    }
};

const handleSelectYearChange = (e) => {
    const value = e.target.value;

    const filteredContracts = $contracts.filter(
        (contract) => contract.year === value
    );
    selectContract.innerHTML = "";
    selectContract.append(new Option("Chọn hợp đồng", ""));
    filteredContracts.forEach((contract) => {
        selectContract.append(new Option(contract.tenhd, contract.id));
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
                `${urlLoadData}?nam=${selectYear.value}&contract_id=${currentContractId}`
            );
            displayStatus(response ?? null);
            displayTable(response?.details ?? null);
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
