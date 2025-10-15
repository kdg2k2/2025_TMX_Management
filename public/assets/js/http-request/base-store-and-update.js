document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("submit-form");
    if (!form) return;

    autoMatchFieldAndFillPatchForm(form, getFormMethod(form), $data);

    // Submit form
    form.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, form);
    });
});
