const createModal = (element) => {
    return new bootstrap.Modal(element, {
        backdrop: "static",
        keyboard: false,
    });
};

const showModal = (element) => {
    const modal = createModal(element);
    modal.show();
};

const hideModal = (element) => {
    const modal = bootstrap.Modal.getOrCreateInstance(element);
    modal.hide();
};
