const assignModal = document.getElementById("assign-modal");
const assignModalForm = assignModal.querySelector("form");
const completeModal = document.getElementById("complete-modal");
const completeModalForm = completeModal.querySelector("form");

const showAssignModal = (btn) => {
    openModalBase(btn, {
        modal: assignModal,
        form: assignModalForm,
    });
};

const showCompleteModal = (btn) => {
    openModalBase(btn, {
        modal: completeModal,
        form: completeModalForm,
    });
};

assignModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(assignModal));
});

completeModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(completeModal));
});
