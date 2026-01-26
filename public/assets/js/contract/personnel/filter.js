const investorId = document.getElementById("investor-id");
const yearContract = document.getElementById("year-contract");
var pathExcel = "";
const renderExcel = async () => {
    const res = await http.get(apiContractPersonnelSynthetic, {
        personnel_id: document.getElementById("personnel-id").value || "",
        investor_id: investorId.value || "",
        contract_id: document.getElementById("contract-id").value || "",
    });

    pathExcel = res?.data || "";
    document
        .getElementById("synthetic-iframe")
        .setAttribute("src", createLinkPreviewFileOnline(pathExcel));
};

const loadContract = async () => {
    const res = await http.get(apiContractList, {
        load_relations: false,
        investor_id: investorId.value || "",
        year: yearContract.value || "",
    });

    fillSelectId("contract-id", res?.data || [], "id", "name");
};

document.getElementById("filter").addEventListener("click", async () => {
    await renderExcel();
});

document.getElementById("download").addEventListener("click", () => {
    if (!pathExcel) {
        alertErr("Tạo file trước!");
        return;
    }
    downloadFileHandler(pathExcel);
});

[investorId, yearContract].forEach((element) => {
    element.addEventListener("change", loadContract);
});

document.addEventListener("DOMContentLoaded", renderExcel);
