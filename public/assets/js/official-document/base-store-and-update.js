const programType = document.getElementById("program-type");
const fields = {
    contract: document.getElementById("contract-id"),
    incoming: document.getElementById("incoming-official-document-id"),
    orther: document.getElementById("other-program-name"),
};
const parentClass = ".col-md-6";
let firstLoad = true;

const resetField = (field) => {
    setHiddenAndRequired(field, false, field.closest(parentClass));
    destroySumoSelect($(field));
    field.value = "";
};

const showField = (field, useSumo = false) => {
    setHiddenAndRequired(field, true, field.closest(parentClass));
    if (useSumo) initSumoSelect($(field));
};

const toggleProgramType = ({ target }) => {
    if (!target) return;

    Object.values(fields).forEach(resetField);

    if (fields[target.value]) {
        showField(
            fields[target.value],
            target.value !== "orther"
        );
    }
};

programType.addEventListener("change", toggleProgramType);

document.addEventListener("DOMContentLoaded", () => {
    programType.dispatchEvent(new Event("change"));

    if (!firstLoad || !$data) return;
    firstLoad = false;

    const map = {
        contract: ["contract_id", true],
        incoming: ["incoming_official_document_id", true],
        orther: ["other_program_name", false],
    };

    const [key, useSumo] = map[$data.program_type] || [];
    if (!key) return;

    const field = fields[$data.program_type];
    destroySumoSelect($(field));
    field.value = $data[key];
    if (useSumo) initSumoSelect($(field));
});
