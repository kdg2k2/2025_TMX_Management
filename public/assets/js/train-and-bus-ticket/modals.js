const approveModal = document.getElementById("approve-modal");
const approveModalForm = approveModal?.querySelector("form");
const rejectModal = document.getElementById("reject-modal");
const rejectModalForm = rejectModal?.querySelector("form");

const showApproveModal = (btn) => {
    openModalBase(btn, {
        modal: approveModal,
        form: approveModalForm,
    });
};

const showRejectModal = (btn) => {
    openModalBase(btn, {
        modal: rejectModal,
        form: rejectModalForm,
    });
};

approveModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(approveModal));
});

rejectModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(rejectModal));
});
