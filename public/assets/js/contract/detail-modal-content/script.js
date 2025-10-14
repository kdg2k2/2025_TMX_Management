const contractDetailModal = document.getElementById("contract-detail-modal");
var contractId = null;
var contractDetail = null;

const loadContractDetail = async (id) => {
    contractDetail = null;
    const res = await http.get(`${showUrl}?id=${id}`);
    if (res.data) contractDetail = res.data;

    renderGerenaInfo();
    renderDocumentsInfo();
};

contractDetailModal.addEventListener("show.bs.modal", async (e) => {
    const relatedTarget = e.relatedTarget;
    contractId = relatedTarget.getAttribute("data-id") ?? null;
    await loadContractDetail(contractId);
});
