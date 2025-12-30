const fixedModal = document.getElementById("fixed-modal");
const fixedModalForm = fixedModal?.querySelector("form");
const repairCosts = fixedModalForm.querySelector('input[name="repair_costs"]');
const showFixedModal = (btn) => {
    openModalBase(btn, {
        modal: fixedModal,
        form: fixedModalForm,
    });
};
fixedModalForm.addEventListener("submit", async (e) => {
    await handleSubmitForm(e, () => hideModal(fixedModal));
});
document.addEventListener("DOMContentLoaded", () => {
    ["input", "paste", "change", "blur"].forEach((evt) => {
        repairCosts.addEventListener(evt, () =>
            updateFormattedSpan(repairCosts, null)
        );
    });
});
