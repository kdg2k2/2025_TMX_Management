const selectYear = document.getElementById("year");
const selectContract = document.getElementById("contract-id");
const countContract = document.querySelector('label[for="contract-id"] span');

const downloadExcelBtn = document.getElementById("download-excel-btn");
const iframe = document.querySelector(".card-body iframe");
var currentPath = null;

const handleDownloadExcel = () => {
    if (currentPath) {
        downloadFileHandler(currentPath);
    } else {
        alertInfo("Chưa có file tổng hợp để tải xuống!");
    }
};

const handleSelectYearChange = (e) => {
    const value = e.target.value;

    const filteredContracts = $contracts.filter(
        (contract) => contract.nam === value
    );
    selectContract.innerHTML = "";
    selectContract.append(new Option("Chọn hợp đồng", ""));
    filteredContracts.forEach((contract) => {
        selectContract.append(new Option(contract.tenhd, contract.id));
    });

    countContract.textContent = `Tổng ${filteredContracts.length}`;
    refreshSumoSelect();

    loadData();
};

const handleContractChange = (e) => {
    loadData();
};

const loadData = async () => {
    displayIframe(null);
    try {
        const response = await http.get(createSyntheticFile, {
            year: selectYear.value,
            contract_id: selectContract.value,
        });
        displayIframe(response.path ?? null);
    } catch (error) {
        console.error("Load data failed:", error);
    }
};

const displayIframe = (path) => {
    if (!path) {
        currentPath = null;
        iframe.setAttribute("src", "");
    } else {
        currentPath = path;
        const pdfUrl = createLinkPreviewFileOnline(currentPath, 1);
        iframe.setAttribute("src", pdfUrl);
    }
};

document.addEventListener("DOMContentLoaded", () => {
    loadData();

    refreshSumoSelect();

    selectYear.addEventListener("change", handleSelectYearChange);
    selectContract.addEventListener("change", handleContractChange);

    downloadExcelBtn.addEventListener("click", handleDownloadExcel);
});
