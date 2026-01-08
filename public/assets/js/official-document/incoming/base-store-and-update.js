const programType = document.getElementById("program-type");
const contractId = document.getElementById("contract-id");
const otherProgramName = document.getElementById("other-program-name");
var firtLoad = true;

const toggleSelectType = (e) => {
    const el = e?.target;
    if (!el) return;
    const parentClass = ".col-md-6";

    const resetField = (field) => {
        setHiddenAndRequired(field, false, field.closest(parentClass));
        destroySumoSelect($(field));
        field.value = "";
    };

    resetField(contractId);
    resetField(otherProgramName);

    const isContract = el.value === "contract";
    const field = isContract ? contractId : otherProgramName;

    setHiddenAndRequired(field, true, field.closest(parentClass));
    if (isContract) initSumoSelect($(contractId));
};

programType.addEventListener("change", (e) => toggleSelectType(e));

document.addEventListener("DOMContentLoaded", () => {
    programType.dispatchEvent(new Event("change"));
    if (firtLoad && $data) {
        firtLoad = false;
        if ($data.program_type == "contract") {
            destroySumoSelect($(contractId));
            contractId.value = $data.contract_id;
            initSumoSelect($(contractId));
        } else {
            otherProgramName.value = $data.other_program_name;
        }
    }
});
