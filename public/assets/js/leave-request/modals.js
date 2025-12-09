const modalApproveRequest = document.getElementById("modal-approve-request");
const modalApproveRequestForm = modalApproveRequest.querySelector("form");
const modalAdjustRequest = document.getElementById("modal-adjust-request");
const modalAdjustRequestForm = modalAdjustRequest.querySelector("form");
const modalAdjustApproveRequest = document.getElementById(
    "modal-adjust-approve-request"
);
const modalAdjustApproveRequestForm =
    modalAdjustApproveRequest.querySelector("form");

const openModalApproveRequest = (btn) => {
    openModalBase(btn, {
        modal: modalApproveRequest,
        form: modalApproveRequestForm,
        inputs: {
            'input[name="approval_status"]': btn.dataset.approveStatus,
        },
    });
};

const openModalAdjustRequest = (btn) => {
    openModalBase(btn, {
        modal: modalAdjustRequest,
        form: modalAdjustRequestForm,
        afterShow: toggleToDateVisibility,
    });
};

const openModalAdjustApproveRequest = (btn) => {
    openModalBase(btn, {
        modal: modalAdjustApproveRequest,
        form: modalAdjustApproveRequestForm,
        inputs: {
            'input[name="adjust_approval_status"]': btn.dataset.approveStatus,
        },
    });
};

modalApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalApproveRequest);
    });
});

modalAdjustRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalAdjustRequest);
    });
});

modalAdjustApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => {
        hideModal(modalAdjustApproveRequest);
    });
});
