const acceptModal = document.getElementById("accept-modal");
const acceptModalFrom = acceptModal.querySelector("form");
const rejectModal = document.getElementById("reject-modal");
const rejectModalFrom = rejectModal.querySelector("form");
const updateStateModal = document.getElementById("update-state-modal");
const updateStateModalFrom = updateStateModal.querySelector("form");

const openAcceptModal = (url) => {
    acceptModalFrom.setAttribute("action", url);
    showModal(acceptModal);
};

const openRejectModal = (url) => {
    rejectModalFrom.setAttribute("action", url);
    showModal(rejectModal);
};

const openUpdateStateModal = (url, data) => {
    updateStateModalFrom.setAttribute("action", url);
    selectValueMapping = {
        state: (item) => item.original,
    };
    inputValueFormatter = {};
    autoMatchFieldAndFillPatchForm(updateStateModalFrom, "patch", data);
    showModal(updateStateModal);
};

acceptModalFrom.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, acceptModalFrom, () => {
        loadList();
        hideModal(acceptModal);
    });
});

rejectModalFrom.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, rejectModalFrom, () => {
        loadList();
        hideModal(rejectModal);
    });
});

updateStateModalFrom.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, updateStateModalFrom, () => {
        loadList();
        hideModal(updateStateModal);
    });
});
