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
    const status = btn.getAttribute("data-approve-status");
    const title = btn.getAttribute("aria-label");
    const action = btn.getAttribute("data-href");
    const onsuccess = btn.getAttribute("data-onsuccess");

    if (status && title && action && onsuccess) {
        modalApproveRequest.querySelector(".modal-title").innerHTML = title;
        modalApproveRequestForm.querySelector(
            'input[name="approval_status"]'
        ).value = status;
        modalApproveRequestForm.setAttribute("action", action);
        modalApproveRequestForm.setAttribute("data-onsuccess", onsuccess);

        showModal(modalApproveRequest);
    }
};

const openModalAdjustRequest = (btn) => {
    const action = btn.getAttribute("data-href");
    const onsuccess = btn.getAttribute("data-onsuccess");

    if (action && onsuccess) {
        modalAdjustRequestForm.setAttribute("action", action);
        modalAdjustRequestForm.setAttribute("data-onsuccess", onsuccess);

        showModal(modalAdjustRequest);
    }
};

const openModalAdjustApproveRequest = (btn) => {
    const status = btn.getAttribute("data-approve-status");
    const title = btn.getAttribute("aria-label");
    const action = btn.getAttribute("data-href");
    const onsuccess = btn.getAttribute("data-onsuccess");

    if (status && title && action && onsuccess) {
        modalAdjustApproveRequest.querySelector(".modal-title").innerHTML =
            title;
        modalAdjustApproveRequestForm.querySelector(
            'input[name="return_approval_status"]'
        ).value = status;
        modalAdjustApproveRequestForm.setAttribute("action", action);
        modalAdjustApproveRequestForm.setAttribute("data-onsuccess", onsuccess);

        showModal(modalAdjustApproveRequest);
    }
};

modalApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalApproveRequestForm, () => {
        hideModal(modalApproveRequest);
    });
});

modalAdjustRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalAdjustRequestForm, () => {
        hideModal(modalAdjustRequest);
    });
});

modalAdjustApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalAdjustApproveRequestForm, () => {
        hideModal(modalAdjustApproveRequest);
    });
});
