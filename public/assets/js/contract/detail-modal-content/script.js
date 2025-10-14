const contractDetailModal = document.getElementById("contract-detail-modal");
var contractDetail = null;

const loadContractDetail = async (id) => {
    contractDetail = null;
    const res = await http.get(`${showUrl}?id=${id}`);
    if (res.data) contractDetail = res.data;

    renderGerenaInfo();
};

contractDetailModal.addEventListener("show.bs.modal", async (e) => {
    const relatedTarget = e.relatedTarget;
    const contractId = relatedTarget.getAttribute("data-id");
    await loadContractDetail(contractId);
});
