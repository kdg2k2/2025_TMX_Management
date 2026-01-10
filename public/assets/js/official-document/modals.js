const reviewApproveModal = document.getElementById("review-approve-modal");
const reviewApproveModalForm = reviewApproveModal.querySelector("form");
const reviewRejectModal = document.getElementById("review-reject-modal");
const reviewRejectModalForm = reviewRejectModal.querySelector("form");
const releaseModal = document.getElementById("release-modal");
const releaseModalForm = releaseModal.querySelector("form");

const showReviewApproveModal = (btn) => {
    openModalBase(btn, {
        modal: reviewApproveModal,
        form: reviewApproveModalForm,
    });
};

const showReviewRejectModal = (btn) => {
    openModalBase(btn, {
        modal: reviewRejectModal,
        form: reviewRejectModalForm,
    });
};

const showReleaseModal = (btn) => {
    openModalBase(btn, {
        modal: releaseModal,
        form: releaseModalForm,
    });
};

reviewApproveModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(reviewApproveModal));
});

reviewRejectModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(reviewRejectModal));
});

releaseModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(releaseModal));
});
