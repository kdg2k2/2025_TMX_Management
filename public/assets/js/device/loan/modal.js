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
