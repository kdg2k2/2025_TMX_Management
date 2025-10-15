const deleteModal = document.getElementById("modalDelete");
const deleteModalForm = deleteModal.querySelector("form");

const openDeleteModal = (triggerEl) => {
    deleteModalForm?.setAttribute(
        "action",
        triggerEl.getAttribute("data-href")
    );
    deleteModalForm?.setAttribute(
        "data-onsuccess",
        triggerEl.getAttribute("data-onsuccess")
    );

    const modal = createModal(deleteModal);
    modal.show();
};

document.addEventListener("DOMContentLoaded", async () => {
    deleteModalForm.addEventListener("submit", async (e) => {
        await handleSubmitForm(e, deleteModalForm, () => {
            hideModal(deleteModal);
        });
    });
});
