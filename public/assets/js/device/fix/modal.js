const fixedModal = document.getElementById("fixed-modal");
const fixedModalForm = fixedModal?.querySelector("form");
const showFixedModal = (btn) => {
    openModalBase(btn, {
        modal: fixedModal,
        form: fixedModalForm,
    });
};
fixedModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(fixedModal));
});
