const contractDetailModal = document.getElementById("contract-detail-modal");
var contractId = (contractDetail = financeId = null);

const loadContractDetail = async () => {
    contractDetail = null;
    const res = await http.get(`${showUrl}?id=${contractId}`);
    if (res.data) contractDetail = res.data;

    renderGerenaInfo();
    renderDocumentsInfo();
    renderBillsInfo();
    renderAppendixesInfo();
    renderFinancesInfo();
};

const createInputWithAttrName = (name) => {
    const input = document.createElement("input");
    input.setAttribute("name", name);
    input.hidden = true;
    return input;
};

const findInputInFormAndSetValue = (form, name, value) => {
    var input = form?.querySelector(`input[name="${name}"]`);
    if (!input) {
        input = createInputWithAttrName(name);
        form.prepend(input);
    }
    input.value = value || "";
    console.log({
        form,
        name,
        value,
        input,
        "input.value": input.value,
    });
};

const appendContractIdInForm = (form) => {
    console.log({contractId});

    findInputInFormAndSetValue(form, "contract_id", contractId);
};

const appendFinanceIdInForm = (form) => {
    console.log({financeId});

    findInputInFormAndSetValue(form, "contract_finance_id", financeId);
};

contractDetailModal.addEventListener("show.bs.modal", async (e) => {
    const relatedTarget = e.relatedTarget;
    contractId = relatedTarget.getAttribute("data-id") ?? null;

    await loadContractDetail();
});
