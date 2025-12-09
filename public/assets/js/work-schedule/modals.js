const modalApproveRequest = document.getElementById("modal-approve-request");
const modalApproveRequestForm = modalApproveRequest.querySelector("form");
const modalReturnRequest = document.getElementById("modal-return-request");
const modalReturnRequestForm = modalReturnRequest.querySelector("form");
const modalReturnApproveRequest = document.getElementById(
    "modal-return-approve-request"
);
const modalReturnApproveRequestForm =
    modalReturnApproveRequest.querySelector("form");

const openModalApproveRequest = (btn) => {
    openModalBase(btn, {
        modal: modalApproveRequest,
        form: modalApproveRequestForm,
        inputs: { 'input[name="approval_status"]': btn.dataset.approveStatus },
    });
};

const openModalReturnRequest = (btn) => {
    openModalBase(btn, {
        modal: modalReturnRequest,
        form: modalReturnRequestForm,
    });
};

const openModalReturnApproveRequest = (btn) => {
    openModalBase(btn, {
        modal: modalReturnApproveRequest,
        form: modalReturnApproveRequestForm,
        inputs: {
            'input[name="return_approval_status"]': btn.dataset.approveStatus,
        },
    });
};

modalApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalApproveRequest);
    });
});

modalReturnRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalReturnRequest);
    });
});

modalReturnApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalReturnApproveRequest);
    });
});
