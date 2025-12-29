const submitForm = document.getElementById("submit-form");
const cloneRowElement = document.getElementById("clone-row");
const btnAddRow = cloneRowElement.querySelector("button");
const today = new Date().toISOString().split("T")[0];

btnAddRow.addEventListener("click", () => {
    const clone = cloneRow(cloneRowElement, submitForm);
    const expectedReturnAt = clone.querySelector(
        'input[name$="[expected_return_at]"]'
    );

    console.log({ clone, expectedReturnAt });

    if (expectedReturnAt) expectedReturnAt.value = today;
});

document.addEventListener("DOMContentLoaded", () => {
    submitForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, () => {
            resetFormRows(submitForm, cloneRowElement);
        });
    });
});
