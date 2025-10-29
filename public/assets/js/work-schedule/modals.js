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

const openModalReturnRequest = (btn) => {
    const action = btn.getAttribute("data-href");
    const onsuccess = btn.getAttribute("data-onsuccess");

    if (action && onsuccess) {
        modalReturnRequestForm.setAttribute("action", action);
        modalReturnRequestForm.setAttribute("data-onsuccess", onsuccess);

        showModal(modalReturnRequest);
    }
};

const openModalReturnApproveRequest = (btn) => {
    const status = btn.getAttribute("data-approve-status");
    const title = btn.getAttribute("aria-label");
    const action = btn.getAttribute("data-href");
    const onsuccess = btn.getAttribute("data-onsuccess");

    if (status && title && action && onsuccess) {
        modalReturnApproveRequest.querySelector(".modal-title").innerHTML =
            title;
        modalReturnApproveRequestForm.querySelector(
            'input[name="return_approval_status"]'
        ).value = status;
        modalReturnApproveRequestForm.setAttribute("action", action);
        modalReturnApproveRequestForm.setAttribute("data-onsuccess", onsuccess);

        showModal(modalReturnApproveRequest);
    }
};

modalApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalApproveRequestForm, () => {
        hideModal(modalApproveRequest);
    });
});

modalReturnRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalReturnRequestForm, () => {
        hideModal(modalReturnRequest);
    });
});

modalReturnApproveRequestForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, modalReturnApproveRequestForm, () => {
        hideModal(modalReturnApproveRequest);
    });
});
