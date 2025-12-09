const selectType = document.querySelector('select[name="type"]');
const selectContract = document.querySelector('select[name="contract_id"]');
const inputOtherProgramName = document.querySelector(
    'input[name="other_program_name"]'
);
const submitForm = document.getElementById("submit-form");
const cloneRowElement = document.getElementById("clone-row");
const btnAddRow = cloneRowElement.querySelector("button");

const setHiddenAndRequired = (element, show = true, parent = null) => {
    if (!element) {
        console.warn("Element is undefined or null");
        return;
    }

    if (!show) {
        element.removeAttribute("required");
        if (parent) parent.hidden = true;
    } else {
        element.setAttribute("required", true);
        if (parent) parent.hidden = false;
    }
};

const toggleSelectType = (e) => {
    const element = e?.target;
    if (!element) return;

    setHiddenAndRequired(
        selectContract,
        false,
        selectContract.closest(".col-md-4")
    );
    setHiddenAndRequired(
        inputOtherProgramName,
        false,
        inputOtherProgramName.closest(".col-md-4")
    );

    if (element.value == "contract") {
        setHiddenAndRequired(
            selectContract,
            true,
            selectContract.closest(".col-md-4")
        );
    } else {
        setHiddenAndRequired(
            inputOtherProgramName,
            true,
            inputOtherProgramName.closest(".col-md-4")
        );
    }
};

const toggleSelectUserType = (e) => {
    const element = e.target;
    if (!element) return;
    const row = element.closest(".col-12.row");
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
    cloneRow(
        cloneRowElement,
        submitForm,
        () => reindexRow(submitForm),
        () => getSelects(),
        () => getMaxRowIndex(getFormFields(submitForm))
    );
});
submitForm.addEventListener("change", (e) => {
    if (e.target.matches("select.user-type")) toggleSelectUserType(e);
});

document.addEventListener("DOMContentLoaded", () => {
    selectType.dispatchEvent(new Event("change"));

    submitForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, ()=>{
            resetFormRows(submitForm, cloneRowElement);
        });
    });
});
