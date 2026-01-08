const selectType = document.querySelector('select[name="type"]');
const selectContract = document.querySelector('select[name="contract_id"]');
const inputOtherProgramName = document.querySelector(
    'input[name="other_program_name"]'
);
const submitForm = document.getElementById("submit-form");
const cloneRowElement = document.getElementById("clone-row");
const btnAddRow = cloneRowElement.querySelector("button");

const toggleSelectType = (e) => {
    const el = e?.target;
    if (!el) return;
    const parentClass = ".col-md-4";

    const reset = (field) => {
        setHiddenAndRequired(field, false, field.closest(parentClass));
        destroySumoSelect($(field));
        field.value = "";
    };

    reset(selectContract);
    reset(inputOtherProgramName);

    const isContract = el.value === "contract";
    const field = isContract ? selectContract : inputOtherProgramName;

    setHiddenAndRequired(field, true, field.closest(parentClass));
    if (isContract) initSumoSelect($(selectContract));
};


const toggleSelectUserType = (e) => {
    const element = e.target;
    if (!element) return;
    const row = element.closest(".clone-row");
    const selectUser = row.querySelector(
        'select[name^="details"][name$="[user_id]"]'
    );
    const inputExternalUserName = row.querySelector(
        'input[name^="details"][name$="[external_user_name]"]'
    );

    setHiddenAndRequired(selectUser, false, selectUser.closest(".col-md-7"));
    setHiddenAndRequired(
        inputExternalUserName,
        false,
        inputExternalUserName.closest(".col-md-7")
    );
    destroySumoSelect($(selectUser));
    selectUser.value = "";
    inputExternalUserName.value = "";

    if (element.value == "internal") {
        setHiddenAndRequired(selectUser, true, selectUser.closest(".col-md-7"));
        initSumoSelect($(selectUser));
    } else {
        setHiddenAndRequired(
            inputExternalUserName,
            true,
            inputExternalUserName.closest(".col-md-7")
        );
    }
};

selectType.addEventListener("change", (e) => toggleSelectType(e));
btnAddRow.addEventListener("click", () => {
    cloneRow(cloneRowElement, submitForm);
});
submitForm.addEventListener("change", (e) => {
    if (e.target.matches("select.user-type")) toggleSelectUserType(e);
});

document.addEventListener("DOMContentLoaded", () => {
    selectType.dispatchEvent(new Event("change"));

    submitForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, () => {
            resetFormRows(submitForm, cloneRowElement);
        });
    });
});
