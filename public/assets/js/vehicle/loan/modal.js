const returnModal = document.getElementById("return-modal");
const returnModalForm = returnModal?.querySelector("form");
const showReturnModal = (btn) => {
    openModalBase(btn, {
        modal: returnModal,
        form: returnModalForm,
    });
};
returnModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(returnModal));
});

document.addEventListener("DOMContentLoaded", () => {
    ["input", "paste", "change", "blur"].forEach((evt) => {
        [
            document.getElementById("fuel-cost"),
            document.getElementById("maintenance-cost"),
        ].forEach((element) => {
            element.addEventListener(evt, () =>
                updateFormattedSpan(element, null)
            );
        });
    });
});
