const selectType = document.querySelector('select[name="type"]');
const selectContract = document.querySelector('select[name="contract"]');
const inputOtherProgramName = document.querySelector(
    'input[name="other_program_name"]'
);
var indexCloneRow = 0;

const setHiddenAndRequired = (element, show = true, parent = null) => {
    if (!element) {
        console.error("Element is undefined or null");
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
    const element = e.target;
    const parent = element.closest(".col-md-4");

    setHiddenAndRequired(selectContract, false, parent);
    setHiddenAndRequired(inputOtherProgramName, false, parent);

    if (element.value == "contract") {
        setHiddenAndRequired(selectContract, true, parent);
    } else {
        setHiddenAndRequired(inputOtherProgramName, true, parent);
    }
};

const toggleSelectUserType = (e) => {
    const element = e.target;
    const parent = element.closest(".col-md-5");
    let selectUser = (inputExternalUserName = null);

    selectUser = parent.querySelector('select[name="details[user]"]');
    inputExternalUserName = parent.querySelector(
        'input[name="details[external_user_name]"]'
    );

    setHiddenAndRequired(selectUser, false, parent);
    setHiddenAndRequired(inputExternalUserName, false, parent);

    if (element.value == "internal") {
        setHiddenAndRequired(selectUser, true, parent);
    } else {
        setHiddenAndRequired(inputExternalUserName, true, parent);
    }
};

selectType.addEventListener("change", toggleSelectType);

document.addEventListener("DOMContentLoaded", () => {
    toggleSelectType();
});
