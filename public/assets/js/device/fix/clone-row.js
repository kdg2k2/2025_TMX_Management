const submitForm = document.getElementById("submit-form");
const cloneRowElement = document.getElementById("clone-row");
const btnAddRow = cloneRowElement.querySelector("button");

btnAddRow.addEventListener("click", () => {
    cloneRow(cloneRowElement, submitForm);
});

document.addEventListener("DOMContentLoaded", () => {
    submitForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, () => {
            resetFormRows(submitForm, cloneRowElement);
        });
    });
});
